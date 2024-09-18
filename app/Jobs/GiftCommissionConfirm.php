<?php

namespace App\Jobs;

use App\Exceptions\BusinessException;
use App\Services\GiftCommissionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GiftCommissionConfirm implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $commissionId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($commissionId)
    {
        $this->commissionId = $commissionId;
        $this->delay(now()->addDays(7));
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            GiftCommissionService::getInstance()->updateToOrderConfirmStatus($this->commissionId);
        } catch (BusinessException $e) {
            Log::error($e->getMessage());
        }
    }
}
