<?php

namespace App\Jobs;

use App\Exceptions\BusinessException;
use App\Services\CouponService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CouponExpire implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $couponId;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($couponId, $expirationTime)
    {
        $this->couponId = $couponId;
        $expirationTime = Carbon::parse($expirationTime);
        $delayInSeconds = $expirationTime->diffInSeconds(Carbon::now());
        $this->delay($delayInSeconds);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            CouponService::getInstance()->expireCoupon($this->couponId);
        } catch (BusinessException $e) {
            Log::error($e->getMessage());
        }
    }
}
