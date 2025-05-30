<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\CartGoods;
use App\Models\Commission;
use App\Models\Coupon;
use App\Models\FreightTemplate;
use App\Models\GiftCommission;
use App\Models\Order;
use App\Models\OrderGoods;
use App\Models\OrderPackageGoods;
use App\Services\AccountService;
use App\Services\AddressService;
use App\Services\CartGoodsService;
use App\Services\CommissionService;
use App\Services\CouponService;
use App\Services\FreightTemplateService;
use App\Services\GiftCommissionService;
use App\Services\GiftGoodsService;
use App\Services\MerchantManagerService;
use App\Services\MerchantPickupAddressService;
use App\Services\OrderGoodsService;
use App\Services\OrderPackageService;
use App\Services\OrderService;
use App\Services\OrderVerifyService;
use App\Services\PromoterService;
use App\Services\RelationService;
use App\Services\TeamCommissionService;
use App\Services\UserCouponService;
use App\Utils\CodeResponse;
use App\Utils\Enums\OrderEnums;
use App\Utils\Inputs\CreateOrderInput;
use App\Utils\Inputs\PageInput;
use App\Utils\WxMpServe;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yansongda\LaravelPay\Facades\Pay;

class OrderController extends Controller
{
    protected $except = ['qrCode'];

    public function preOrderInfo()
    {
        $cartGoodsIds = $this->verifyArrayNotEmpty('cartGoodsIds');
        $deliveryMode = $this->verifyInteger('deliveryMode', 1);
        $addressId = $this->verifyId('addressId');
        $couponId = $this->verifyId('couponId');
        $useBalance = $this->verifyBoolean('useBalance', false);

        $cartGoodsListColumns = ['goods_id', 'cover', 'name', 'is_gift', 'freight_template_id', 'selected_sku_name', 'price', 'number', 'delivery_method'];
        $cartGoodsList = CartGoodsService::getInstance()->getCartGoodsListByIds($this->userId(), $cartGoodsIds, $cartGoodsListColumns);

        $address = null;
        if ($deliveryMode == 1) {
            $addressColumns = ['id', 'name', 'mobile', 'region_code_list', 'region_desc', 'address_detail'];
            if (is_null($addressId)) {
                /** @var Address $address */
                $address = AddressService::getInstance()->getDefaultAddress($this->userId(), $addressColumns);
            } else {
                /** @var Address $address */
                $address = AddressService::getInstance()->getUserAddressById($this->userId(), $addressId, $addressColumns);
            }

            $freightTemplateIds = $cartGoodsList->pluck('freight_template_id')->toArray();
            $freightTemplateList = FreightTemplateService::getInstance()
                ->getListByIds($freightTemplateIds)
                ->map(function (FreightTemplate $freightTemplate) {
                    $freightTemplate->area_list = json_decode($freightTemplate->area_list);
                    return $freightTemplate;
                })->keyBy('id');
        }

        $errMsg = '';
        $totalFreightPrice = 0;
        $couponDenomination = 0;
        $deductionBalance = 0;
        $totalPrice = 0;
        $totalNumber = 0;

        // 优惠券逻辑
        $couponList = $this->getCouponList($cartGoodsList);
        if (count($couponList) != 0) {
            if (is_null($couponId)) {
                $couponDenomination = $couponList->first()->denomination;
            } else if ($couponId != 0) {
                $couponDenomination = $couponList->keyBy('id')->get($couponId)->denomination;
            }
        }

        foreach ($cartGoodsList as $cartGoods) {
            $price = bcmul($cartGoods->price, $cartGoods->number, 2);
            $totalPrice = bcadd($totalPrice, $price, 2);
            $totalNumber = $totalNumber + $cartGoods->number;

            // 计算运费
            if ($deliveryMode == 1) {
                if (is_null($address) || $cartGoods->freight_template_id == 0) {
                    $freightPrice = 0;
                } else {
                    /** @var FreightTemplate $freightTemplate */
                    $freightTemplate = $freightTemplateList->get($cartGoods->freight_template_id);
                    if ($freightTemplate->free_quota != 0 && $price > $freightTemplate->free_quota) {
                        $freightPrice = 0;
                    } else {
                        $cityCode = json_decode($address->region_code_list)[1];
                        if (strlen($cityCode) != 6) {
                            $errMsg = '收货地址异常，请编辑更新地址，建议手动获取地址省市区';
                            $freightPrice = 0;
                        } else {
                            $area = collect($freightTemplate->area_list)->first(function ($area) use ($cityCode) {
                                return in_array(substr($cityCode, 0, 4), explode(',', $area->pickedCityCodes));
                            });
                            if (is_null($area)) {
                                $errMsg = '商品"' . $cartGoods->name . '"暂不支持配送至当前地址，请更换收货地址';
                                $freightPrice = 0;
                            } else {
                                if ($freightTemplate->compute_mode == 1) {
                                    $freightPrice = $area->fee;
                                } else {
                                    $freightPrice = bcmul($area->fee, $cartGoods->number, 2);
                                }
                            }
                        }
                    }
                }
                $totalFreightPrice = bcadd($totalFreightPrice, $freightPrice, 2);
            }
        }

        $paymentAmount = bcadd($totalPrice, $totalFreightPrice, 2);
        $paymentAmount = bcsub($paymentAmount, $couponDenomination, 2);

        // 余额逻辑
        $account = AccountService::getInstance()->getUserAccount($this->userId());
        $accountBalance = $account->status == 1 ? $account->balance : 0;
        if ($useBalance) {
            $deductionBalance = min($paymentAmount, $accountBalance);
            $paymentAmount = bcsub($paymentAmount, $deductionBalance, 2);
        }

        return $this->success([
            'errMsg' => $errMsg,
            'addressInfo' => $address,
            'goodsList' => $cartGoodsList,
            'freightPrice' => $totalFreightPrice,
            'couponList' => $couponList,
            'couponDenomination' => $couponDenomination,
            'totalPrice' => $totalPrice,
            'totalNumber' => $totalNumber,
            'accountBalance' => $accountBalance,
            'deductionBalance' => $deductionBalance,
            'paymentAmount' => $paymentAmount
        ]);
    }

    private function getCouponList($cartGoodsList)
    {
        $couponIds = UserCouponService::getInstance()->getUserCouponList($this->userId())->pluck('coupon_id')->toArray();
        $couponList = CouponService::getInstance()->getAvailableCouponListByIds($couponIds)->keyBy('goods_id');
        $giftGoodsIds = GiftGoodsService::getInstance()->getGoodsList([1, 2])->pluck('goods_id')->toArray();
        return $cartGoodsList->map(function (CartGoods $cartGoods) use ($giftGoodsIds, $couponList) {
            // 礼包商品不可使用优惠券
            if (in_array($cartGoods->goods_id, $giftGoodsIds)) {
                return null;
            }

            /** @var Coupon $coupon */
            $coupon = $couponList->get($cartGoods->goods_id);
            if (!is_null($coupon)) {
                switch ($coupon->type) {
                    case 1:
                        return $coupon;
                    case 2:
                        if ($cartGoods->number >= $coupon->num_limit) {
                            return $coupon;
                        } else {
                            return null;
                        }
                    case 3:
                        if (bcmul($cartGoods->price, $cartGoods->number, 2) >= $coupon->price_limit) {
                            return $coupon;
                        } else {
                            return null;
                        }
                }
            }
            return null;
        })->filter()->sortBy('denomination');
    }

    public function submit()
    {
        /** @var CreateOrderInput $input */
        $input = CreateOrderInput::new();

        // 分布式锁，防止重复请求
        $lockKey = sprintf('create_order_%s_%s', $this->userId(), md5(serialize($input)));
        $lock = Cache::lock($lockKey, 5);
        if (!$lock->get()) {
            $this->fail(CodeResponse::FAIL, '请勿重复提交订单');
        }

        $orderIds = DB::transaction(function () use ($input) {
            // 1.获取地址
            $address = null;
            if ($input->deliveryMode == 1) {
                $address = AddressService::getInstance()->getUserAddressById($this->userId(), $input->addressId);
                if (is_null($address)) {
                    return $this->fail(CodeResponse::NOT_FOUND, '用户地址不存在');
                }
            }

            // 2.获取优惠券
            $coupon = null;
            if (!is_null($input->couponId) && $input->couponId != 0) {
                $userCoupon = UserCouponService::getInstance()->getUserCoupon($this->userId(), $input->couponId);
                if (is_null($userCoupon)) {
                    return $this->fail(CodeResponse::NOT_FOUND, '优惠券无法使用');
                }
                $coupon = CouponService::getInstance()->getAvailableCouponById($input->couponId);
                if (is_null($coupon)) {
                    return $this->fail(CodeResponse::NOT_FOUND, '优惠券不存在');
                }
            }

            // 3.判断余额状态
            if (!is_null($input->useBalance) && $input->useBalance != 0) {
                $account = AccountService::getInstance()->getUserAccount($this->userId());
                if ($account->status == 0 || $account->balance <= 0) {
                    return $this->fail(CodeResponse::NOT_FOUND, '余额异常不可用，请联系客服解决问题');
                }
            }

            // 4.获取购物车商品
            $cartGoodsList = CartGoodsService::getInstance()->getCartGoodsListByIds($this->userId(), $input->cartGoodsIds);

            // 5.获取运费模板列表
            $freightTemplateList = null;
            if ($input->deliveryMode == 1) {
                $freightTemplateIds = $cartGoodsList->pluck('freight_template_id')->toArray();
                $freightTemplateList = FreightTemplateService::getInstance()
                    ->getListByIds($freightTemplateIds)
                    ->map(function (FreightTemplate $freightTemplate) {
                        $freightTemplate->area_list = json_decode($freightTemplate->area_list);
                        return $freightTemplate;
                    })->keyBy('id');
            }

            // 6.按商家进行订单拆分，生成对应订单
            $merchantIds = collect(array_unique($cartGoodsList->pluck('merchant_id')->toArray()));

            $promoterInfo = $this->user()->promoterInfo;
            $superiorId = $this->user()->superiorId();
            $userId = $this->userId();

            $superiorPromoterInfo = null;
            $managerId = null;
            $managerPromoterInfo = null;
            if (!is_null($superiorId)) {
                $superiorPromoterInfo = PromoterService::getInstance()->getPromoterByUserId($superiorId);
                $managerId = RelationService::getInstance()->getSuperiorId($superiorId);
                if (!is_null($managerId)) {
                    $managerPromoterInfo = PromoterService::getInstance()->getPromoterByUserId($superiorId);
                }
            }

            $orderIds = $merchantIds->map(function ($merchantId) use (
                $managerId,
                $superiorId,
                $promoterInfo,
                $superiorPromoterInfo,
                $managerPromoterInfo,
                $userId,
                $coupon,
                $address,
                $cartGoodsList,
                $freightTemplateList,
                $input
            ) {
                $filterCartGoodsList = $cartGoodsList->groupBy('merchant_id')->get($merchantId);
                $orderId = OrderService::getInstance()->createOrder($userId, $merchantId, $filterCartGoodsList, $input, $freightTemplateList, $address, $coupon);

                // 7.生成订单商品快照
                OrderGoodsService::getInstance()->createList($filterCartGoodsList, $orderId, $userId);

                /** @var CartGoods $cartGoods */
                foreach ($filterCartGoodsList as $cartGoods) {
                    if ($cartGoods->is_gift && is_null($promoterInfo)) {
                        // 8.礼包佣金逻辑（前提：礼包商品，普通用户）
                        // 场景1：普通用户没有上级 - 生成佣金记录，只作为记录用
                        // 场景2：普通用户上级为推广员，没有上上级，或上上级也为推广员 - 生成15%上级佣金的佣金记录
                        // 场景3：普通用户上级为推广员，上上级为C级 - 生成包含15%上级佣金、5%上上级佣金的佣金记录
                        // 场景4：普通用户上级为C级 - 生成包含20%上级佣金的佣金记录
                        GiftCommissionService::getInstance()->createCommission($userId, $orderId, $cartGoods, $superiorId, $managerId);
                    } else {
                        // 9.生成商品佣金记录（前提：非礼包商品）
                        // 场景1：普通用户且没有上级 - 不需要生成佣金记录
                        // 场景2：普通用户拥有上级 - 生成"分享场景"佣金记录
                        // 场景3：推广员 - 生成"自购场景"佣金记录
                        if (!is_null($promoterInfo) || !is_null($superiorId)) {
                            $scene = !is_null($promoterInfo) ? 1 : 2;
                            $superiorId = !is_null($promoterInfo) ? null : $superiorId;
                            CommissionService::getInstance()->createCommission($scene, $userId, $orderId, $cartGoods, $superiorId, $coupon);
                        }

                        // 10.生成团队佣金记录（前提：非礼包商品）
                        // 场景1：推广员 -> 推广员 -> 普通用户下单
                        // 场景2：推广员 -> 推广员下单
                        if (!is_null($superiorPromoterInfo)) {
                            if (!is_null($managerPromoterInfo)) {
                                TeamCommissionService::getInstance()->createCommission($managerPromoterInfo->user_id, $managerPromoterInfo->level, $userId, $orderId, $cartGoods, $coupon);
                            } else {
                                TeamCommissionService::getInstance()->createCommission($superiorPromoterInfo->user_id, $superiorPromoterInfo->level, $userId, $orderId, $cartGoods, $coupon);
                            }
                        }
                    }
                }

                return $orderId;
            });

            // 11.清空购物车
            CartGoodsService::getInstance()->deleteCartGoodsList($this->userId(), $input->cartGoodsIds);

            // 12.使用优惠券
            if (!is_null($input->couponId)) {
                UserCouponService::getInstance()->useCoupon($this->userId(), $input->couponId);
            }

            return $orderIds;
        });

        return $this->success($orderIds);
    }

    public function payParams()
    {
        $orderIds = $this->verifyArrayNotEmpty('orderIds');
        $order = OrderService::getInstance()->createWxPayOrder($this->userId(), $orderIds, $this->user()->openid);
        $payParams = null;
        if ($order['total_fee'] == 0) {
            $orderList = OrderService::getInstance()->getUnpaidListByIds($orderIds);
            OrderService::getInstance()->paySuccess($orderList);
        } else {
            $payParams = Pay::wechat()->miniapp($order);
        }
        return $this->success($payParams);
    }

    public function orderListTotals()
    {
        return $this->success([
            OrderService::getInstance()->getListTotal($this->userId(), $this->statusList(1)),
            OrderService::getInstance()->getListTotal($this->userId(), $this->statusList(2)),
            OrderService::getInstance()->getListTotal($this->userId(), $this->statusList(3)),
            OrderService::getInstance()->getListTotal($this->userId(), $this->statusList(4)),
            OrderService::getInstance()->getListTotal($this->userId(), [OrderEnums::STATUS_REFUND]),
        ]);
    }

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $status = $this->verifyRequiredInteger('status');

        $statusList = $this->statusList($status);
        $page = OrderService::getInstance()->getOrderListByStatus($this->userId(), $statusList, $input);
        $orderList = collect($page->items());
        $list = $this->handleOrderList($orderList);

        return $this->success($this->paginate($page, $list));
    }

    private function statusList($status) {
        switch ($status) {
            case 1:
                $statusList = [OrderEnums::STATUS_CREATE];
                break;
            case 2:
                $statusList = [OrderEnums::STATUS_PAY, OrderEnums::STATUS_EXPORTED];
                break;
            case 3:
                $statusList = [OrderEnums::STATUS_SHIP, OrderEnums::STATUS_PENDING_VERIFICATION];
                break;
            case 4:
                $statusList = [OrderEnums::STATUS_CONFIRM, OrderEnums::STATUS_AUTO_CONFIRM, OrderEnums::STATUS_ADMIN_CONFIRM];
                break;
            case 5:
                $statusList = [OrderEnums::STATUS_REFUND, OrderEnums::STATUS_REFUND_CONFIRM];
                break;
            case 6:
                $statusList = [OrderEnums::STATUS_FINISHED];
                break;
            default:
                $statusList = [];
                break;
        }

        return $statusList;
    }

    public function search()
    {
        $keywords = $this->verifyRequiredString('keywords');

        $orderGoodsList = OrderGoodsService::getInstance()->searchList($this->userId(), $keywords);
        $orderIds = $orderGoodsList->pluck('order_id')->toArray();
        $orderList = OrderService::getInstance()->getOrderListByIds($orderIds);
        $list = $this->handleOrderList($orderList);

        return $this->success($list);
    }

    private function handleOrderList($orderList)
    {
        $orderIds = $orderList->pluck('id')->toArray();
        $goodsListColumns = ['order_id', 'goods_id', 'merchant_id', 'is_gift', 'refund_status', 'cover', 'name', 'selected_sku_name', 'price', 'number'];
        $groupedGoodsList = OrderGoodsService::getInstance()->getListByOrderIds($orderIds, $goodsListColumns)->groupBy('order_id');
        return $orderList->map(function (Order $order) use ($groupedGoodsList) {
            $goodsList = $groupedGoodsList->get($order->id);
            return [
                'id' => $order->id,
                'status' => $order->status,
                'statusDesc' => OrderEnums::STATUS_TEXT_MAP[$order->status],
                'goodsList' => $goodsList,
                'paymentAmount' => $order->payment_amount,
                'refundAmount' => $order->refund_amount,
                'consignee' => $order->consignee,
                'mobile' => $order->mobile,
                'address' => $order->address,
                'orderSn' => $order->order_sn,
                'payTime' => $order->pay_time,
                'finishTime' => $order->finish_time,
                'createdAt' => $order->created_at
            ];
        });
    }

    public function cancel()
    {
        $id = $this->verifyRequiredId('id');
        OrderService::getInstance()->userCancel($this->userId(), $id);
        return $this->success();
    }

    public function qrCode()
    {
        $code = $this->verifyRequiredId('code');
        $qrCode = QrCode::format('png')->size(400)->generate($code);
        return response($qrCode)->header('Content-Type', 'image/png');
    }

    public function verifyCode()
    {
        $orderId = $this->verifyRequiredId('orderId');
        $verifyCode = OrderVerifyService::getInstance()->getByOrderId($orderId)->verify_code;
        return $this->success($verifyCode);
    }

    public function verify()
    {
        $code = $this->verifyRequiredString('code');

        $verifyInfo = OrderVerifyService::getInstance()->getByCode($code);
        if (is_null($verifyInfo)) {
            return $this->fail(CodeResponse::PARAM_VALUE_ILLEGAL, '无效核销码');
        }

        $order = OrderService::getInstance()->getPendingVerifyOrderById($verifyInfo->order_id);
        if (is_null($order)) {
            return $this->fail(CodeResponse::PARAM_VALUE_ILLEGAL, '订单不存在');
        }

        $managerIds = MerchantManagerService::getInstance()->getManagerList($order->merchant_id)->pluck('user_id')->toArray();
        if (!in_array($this->userId(), $managerIds)) {
            return $this->fail(CodeResponse::PARAM_VALUE_ILLEGAL, '非当前商家核销员，无法核销');
        }

        DB::transaction(function () use ($verifyInfo, $order) {
            OrderService::getInstance()->userConfirm($order->user_id, $order->id);
            OrderVerifyService::getInstance()->verified($verifyInfo->id, $this->userId());
        });

        return $this->success();
    }

    public function confirm()
    {
        $id = $this->verifyRequiredId('id');
        DB::transaction(function () use ($id) {
            OrderService::getInstance()->userConfirm($this->userId(), $id);
        });
        return $this->success();
    }

    public function delete()
    {
        $ids = $this->verifyArrayNotEmpty('ids', []);
        $orderList = OrderService::getInstance()->getUserOrderList($this->userId(), $ids);
        if (count($orderList) == 0) {
            return $this->fail(CodeResponse::PARAM_VALUE_ILLEGAL, '订单不存在');
        }
        DB::transaction(function () use ($orderList) {
            OrderService::getInstance()->delete($orderList);
        });
        return $this->success();
    }

    public function refund()
    {
        $id = $this->verifyRequiredId('id');
        OrderService::getInstance()->userRefund($this->userId(), $id);
        return $this->success();
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $columns = [
            'id',
            'order_sn',
            'status',
            'user_id',
            'delivery_mode',
            'consignee',
            'mobile',
            'address',
            'pickup_address_id',
            'pickup_time',
            'pickup_mobile',
            'goods_price',
            'freight_price',
            'coupon_id',
            'coupon_denomination',
            'deduction_balance',
            'payment_amount',
            'pay_time',
            'ship_channel',
            'ship_sn',
            'ship_time',
            'confirm_time',
            'finish_time',
            'refund_amount',
            'remarks',
            'created_at',
            'updated_at',
        ];
        $order = OrderService::getInstance()->getOrderById($id, $columns);
        if (is_null($order)) {
            return $this->fail(CodeResponse::NOT_FOUND, '订单不存在');
        }

        $goodsList = OrderGoodsService::getInstance()->getListByOrderId($order->id);
        $order['goods_list'] = $goodsList;

        $packageList = OrderPackageService::getInstance()->getListByOrderId($order->id);
        $order['package_list'] = $packageList ?: [];

        if ($order->delivery_mode == 2) {
            $pickupAddress = MerchantPickupAddressService::getInstance()
                ->getAddressById($order->pickup_address_id, ['id', 'name', 'address_detail', 'latitude', 'longitude']);
            $order['pickup_address'] = $pickupAddress;
            unset($order['pickup_address_id']);

            $verifyInfo = OrderVerifyService::getInstance()->getByOrderId($order->id);
            $order['verify_code'] = $verifyInfo->verify_code ?: null;
        }

        return $this->success($order);
    }

    public function commissionOrderList()
    {
        $scene = $this->verifyInteger('scene');
        $timeType = $this->verifyRequiredInteger('timeType');
        $statusList = $this->verifyArray('statusList');
        /** @var PageInput $input */
        $input = PageInput::new();

        $commissionList = CommissionService::getInstance()->getUserCommissionListByTimeType($this->userId(), $timeType, $statusList,$scene ?: null);
        $groupCommissionList = $commissionList->groupBy('order_id');
        $keyCommissionList = $commissionList->mapWithKeys(function ($commission) {
            return [ $commission->order_id . '_' . $commission->goods_id => $commission ];
        });
        $orderIds = $commissionList->pluck('order_id')->toArray();

        $goodsIds = $commissionList->pluck('goods_id')->toArray();
        $goodsColumns = ['order_id', 'goods_id', 'cover', 'name', 'selected_sku_name', 'price', 'number'];
        $groupGoodsList = OrderGoodsService::getInstance()->getListByGoodsIds($goodsIds, $goodsColumns)->groupBy('order_id');

        $page = OrderService::getInstance()->getOrderPageByIds($orderIds, $input);
        $list = collect($page->items())->map(function (Order $order) use ($groupGoodsList, $keyCommissionList, $groupCommissionList) {
            $orderCommissionList = $groupCommissionList->get($order->id);
            $commissionBaseSum = $orderCommissionList->sum('commission_base');
            $commissionAmountSum = $orderCommissionList->sum('commission_amount');
            /** @var Commission $firstCommission */
            $firstCommission = $orderCommissionList->first();

            $orderGoodsList = $groupGoodsList->get($order->id);
            $orderGoodsList->map(function (OrderGoods $goods) use ($order, $keyCommissionList) {
                $commissionKey = $order->id . '_' . $goods->goods_id;
                /** @var Commission $commission */
                $commission = $keyCommissionList->get($commissionKey);
                $goods['commission'] = $commission->commission_amount;
                unset($goods->order_id);
                return $goods;
            });

            return [
                'id' => $order->id,
                'orderSn' => $order->order_sn,
                'status' => $firstCommission->status,
                'createdAt' => $order->created_at,
                'commissionBase' => $commissionBaseSum,
                'commissionAmount' => bcadd($commissionAmountSum, 0, 2) ,
                'scene' => $firstCommission->scene,
                'goodsList' => $orderGoodsList
            ];
        });

        return $this->success($this->paginate($page, $list));
    }

    public function teamCommissionOrderList()
    {
        $timeType = $this->verifyRequiredInteger('timeType');
        $statusList = $this->verifyArray('statusList');
        /** @var PageInput $input */
        $input = PageInput::new();

        $commissionList = TeamCommissionService::getInstance()->getUserCommissionListByTimeType($this->userId(), $timeType, $statusList);
        $groupCommissionList = $commissionList->groupBy('order_id');
        $keyCommissionList = $commissionList->keyBy('goods_id');
        $orderIds = $commissionList->pluck('order_id')->toArray();

        $goodsIds = $commissionList->pluck('goods_id')->toArray();
        $goodsColumns = ['order_id', 'goods_id', 'cover', 'name', 'selected_sku_name', 'price', 'number'];
        $groupGoodsList = OrderGoodsService::getInstance()->getListByGoodsIds($goodsIds, $goodsColumns)->groupBy('order_id');

        $page = OrderService::getInstance()->getOrderPageByIds($orderIds, $input);
        $list = collect($page->items())->map(function (Order $order) use ($groupGoodsList, $keyCommissionList, $groupCommissionList) {
            $orderCommissionList = $groupCommissionList->get($order->id);
            $commissionBaseSum = $orderCommissionList->sum('commission_base');
            $commissionAmountSum = $orderCommissionList->sum('commission_amount');
            /** @var Commission $firstCommission */
            $firstCommission = $orderCommissionList->first();

            $orderGoodsList = $groupGoodsList->get($order->id);
            $orderGoodsList->map(function (OrderGoods $goods) use ($keyCommissionList) {
                /** @var Commission $commission */
                $commission = $keyCommissionList->get($goods->goods_id);
                $goods['commission'] = $commission->commission_amount;
                unset($goods->order_id);
                return $goods;
            });

            return [
                'id' => $order->id,
                'orderSn' => $order->order_sn,
                'status' => $firstCommission->status,
                'createdAt' => $order->created_at,
                'commissionBase' => $commissionBaseSum,
                'commissionAmount' => $commissionAmountSum,
                'goodsList' => $orderGoodsList
            ];
        });

        return $this->success($this->paginate($page, $list));
    }

    public function giftCommissionOrderList()
    {
        $timeType = $this->verifyRequiredInteger('timeType');
        /** @var PageInput $input */
        $input = PageInput::new();

        $commissionList = GiftCommissionService::getInstance()->getListByTimeType($this->userId(), $timeType);
        $orderIds = $commissionList->pluck('order_id')->toArray();

        $goodsIds = $commissionList->pluck('goods_id')->toArray();
        $goodsColumns = ['order_id', 'goods_id', 'cover', 'name', 'selected_sku_name', 'price', 'number'];
        $goodsList = OrderGoodsService::getInstance()->getListByGoodsIds($goodsIds, $goodsColumns)->keyBy('order_id');

        $page = OrderService::getInstance()->getOrderPageByIds($orderIds, $input);
        $list = collect($page->items())->map(function (Order $order) use ($commissionList, $goodsList) {
            /** @var GiftCommission $commission */
            $commission = $commissionList->keyBy('order_id')->get($order->id);
            $commissionSum = $commission->promoter_id == $this->userId() ? $commission->promoter_commission : $commission->manager_commission;
            $goods = $goodsList->get($order->id);

            return [
                'id' => $order->id,
                'orderSn' => $order->order_sn,
                'status' => $commission->status,
                'createdAt' => $order->created_at,
                'commission' => $commissionSum,
                'goods' => $goods
            ];
        });

        return $this->success($this->paginate($page, $list));
    }

    public function waybillToken()
    {
        $id = $this->verifyRequiredId('id');
        $package = OrderPackageService::getInstance()->getPackageById($id);

        if (!is_null($package)) {
            $order = OrderService::getInstance()->getOrderById($package->order_id);
            if (is_null($order)) {
                return $this->fail(CodeResponse::NOT_FOUND, '订单不存在');
            }
            $goodsList = $package->goodsList->map(function (OrderPackageGoods $goods) {
                return (object) [
                    'id' => $goods->goods_id,
                    'cover' => $goods->goods_cover,
                    'name' => $goods->goods_name,
                ];
            });
            $token = WxMpServe::new()->getWaybillToken($this->user()->openid, $package->ship_code, $package->ship_sn, $goodsList, $order);
        } else {
            // todo 旧逻辑兼容处理
            $order = OrderService::getInstance()->getOrderById($id);
            if (is_null($order)) {
                return $this->fail(CodeResponse::NOT_FOUND, '订单不存在');
            }
            $token = WxMpServe::new()->getWaybillToken($this->user()->openid, $order->ship_code, $order->ship_sn, $order->goodsList, $order);
        }

        return $this->success($token);
    }

    public function modifyOrderAddressInfo()
    {
        $orderId = $this->verifyRequiredInteger('orderId');
        $addressId = $this->verifyRequiredInteger('addressId');
        $order = OrderService::getInstance()->modifyAddressInfo($this->userId(), $orderId, $addressId);

        return $this->success([
            'consignee' => $order->consignee,
            'mobile' => $order->mobile,
            'address' => $order->address,
        ]);
    }
}
