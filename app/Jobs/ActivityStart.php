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

class ActivityStart implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $activityId;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($activityId, $startTime)
    {
        $this->activityId = $activityId;
        $startTime = Carbon::parse($startTime);
        $delayInSeconds = $startTime->diffInSeconds(Carbon::now());
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
            ActivityService::getInstance()->startActivity($this->activityId);
        } catch (BusinessException $e) {
            Log::error($e->getMessage());
        }
    }
}
