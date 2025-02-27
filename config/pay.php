<?php

return [
    'alipay' => [
        // 支付宝分配的 APPID
        'app_id' => env('ALI_APP_ID', ''),

        // 支付宝异步通知地址
        'notify_url' => '',

        // 支付成功后同步通知地址
        'return_url' => '',

        // 阿里公共密钥，验证签名时使用
        'ali_public_key' => env('ALI_PUBLIC_KEY', ''),

        // 自己的私钥，签名时使用
        'private_key' => env('ALI_PRIVATE_KEY', ''),

        // 使用公钥证书模式，请配置下面两个参数，同时修改 ali_public_key 为以 .crt 结尾的支付宝公钥证书路径，如（./cert/alipayCertPublicKey_RSA2.crt）
        // 应用公钥证书路径
        // 'app_cert_public_key' => './cert/appCertPublicKey.crt',

        // 支付宝根证书路径
        // 'alipay_root_cert' => './cert/alipayRootCert.crt',

        // optional，默认 warning；日志路径为：sys_get_temp_dir().'/logs/yansongda.pay.log'
        'log' => [
            'file' => storage_path('logs/alipay.log'),
        //  'level' => 'debug'
        //  'type' => 'single', // optional, 可选 daily.
        //  'max_file' => 30,
        ],

        // optional，设置此参数，将进入沙箱模式
        // 'mode' => 'dev',
    ],

    'wechat' => [
        // 公众号 APPID
        'app_id' => env('WX_APPID', ''),

        // 小程序 APPID
        'miniapp_id' => env('WX_MP_APPID', ''),

        // APP 引用的 appid
        'appid' => '',

        // 微信支付分配的微信商户号
        'mch_id' => env('WX_PAY_MCH_ID', ''),

        // 微信支付异步通知地址
        'notify_url' => env('APP_URL') . 'wx/pay_notify',

        // 微信支付签名秘钥
        'key' => env('WX_PAY_KEY', ''),

        // 客户端证书路径，退款、红包等需要用到。请填写绝对路径，linux 请确保权限问题。pem 格式。
        'cert_client' => storage_path('app/wxpay/apiclient_cert.pem'),

        // 客户端秘钥路径，退款、红包等需要用到。请填写绝对路径，linux 请确保权限问题。pem 格式。
        'cert_key' => storage_path('app/wxpay/apiclient_key.pem'),

        // optional，默认 warning；日志路径为：sys_get_temp_dir().'/logs/yansongda.pay.log'
        'log' => [
            'file' => storage_path('logs/wechat.log'),
        //  'level' => 'debug'
        //  'type' => 'single', // optional, 可选 daily.
        //  'max_file' => 30,
        ],

        // optional
        // 'dev' 时为沙箱模式
        // 'hk' 时为东南亚节点
        // 'mode' => 'dev',
    ],
];
