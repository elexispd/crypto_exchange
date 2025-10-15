<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RunQueueWorker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:work-shared
                            {--time=55 : Maximum runtime in minutes}
                            {--sleep=1 : Sleep time between jobs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run queue worker optimized for shared hosting';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $maxRuntime = $this->option('time') * 60; // Convert to seconds
        $sleepTime = $this->option('sleep');
        $startTime = time();

        $this->info("Starting queue worker for shared hosting...");
        $this->info("Max runtime: {$this->option('time')} minutes");
        $this->info("Sleep time: {$sleepTime} seconds");

        Log::info('Queue worker started', [
            'max_runtime' => $this->option('time'),
            'sleep_time' => $sleepTime
        ]);

        while ((time() - $startTime) < $maxRuntime) {
            try {
                $this->info('Processing next job...');

                // Process one job at a time
                $this->call('queue:work', [
                    '--once' => true,
                    '--tries' => 3,
                    '--timeout' => 60,
                    '--queue' => 'default,emails', // Process default and emails queues
                ]);

                // Sleep between jobs to prevent CPU overload
                if ($sleepTime > 0) {
                    sleep($sleepTime);
                }

            } catch (\Exception $e) {
                $this->error('Queue worker error: ' . $e->getMessage());
                Log::error('Queue worker error', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                // Sleep longer on error
                sleep(5);
            }
        }

        $this->info('Queue worker completed its cycle.');
        Log::info('Queue worker completed cycle', [
            'total_runtime' => time() - $startTime
        ]);

        return Command::SUCCESS;
    }
}
