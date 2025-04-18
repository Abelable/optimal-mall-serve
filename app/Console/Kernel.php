<?php

namespace App\Console;

use App\Services\ActivityService;
use App\Services\CouponService;
use App\Services\OrderService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->call(function () {
            OrderService::getInstance()->systemCancel();
            OrderService::getInstance()->systemConfirm();
            OrderService::getInstance()->systemFinish();
            OrderService::getInstance()->confirmMissCommission();
            CouponService::getInstance()->handelExpiredCoupons();
            ActivityService::getInstance()->handelExpiredActivity();
        })->dailyAt('03:00')->runInBackground()->name('order_system_confirm')->onOneServer();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
