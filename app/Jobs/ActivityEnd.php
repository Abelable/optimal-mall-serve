<?php

namespace App\Jobs;

use App\Exceptions\BusinessException;
use App\Services\ActivityService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ActivityEnd implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $activityId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($activityId, $endTime)
    {
        $this->activityId = $activityId;
        $this->delay(Carbon::createFromFormat('Y-m-d\TH:i:s.u\Z', $endTime)->setTimezone('UTC')->toDateTimeString());
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            ActivityService::getInstance()->endActivity($this->activityId);
        } catch (BusinessException $e) {
            Log::error($e->getMessage());
        }
    }
}
