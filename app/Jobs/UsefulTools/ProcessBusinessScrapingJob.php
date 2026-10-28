<?php

namespace App\Jobs\UsefulTools;

use App\Models\UsefulTools\UsefulToolScrapingJob;
use App\Services\UsefulTools\BusinessData\BusinessDataScraperService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessBusinessScrapingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 120;

    public function __construct(public UsefulToolScrapingJob $jobRecord)
    {
    }

    public function handle(BusinessDataScraperService $scraperService): void
    {
        try {
            $scraperService->executeJob($this->jobRecord);
        } catch (\Throwable $e) {
            Log::error('ProcessBusinessScrapingJob failed', [
                'job_id' => $this->jobRecord->id,
                'error' => $e->getMessage(),
            ]);

            $this->jobRecord->update([
                'status' => 'failed',
                'error_message' => 'Job execution error: ' . $e->getMessage(),
                'completed_at' => now(),
            ]);
        }
    }
}
