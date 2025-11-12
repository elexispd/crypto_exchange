<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ClearOptimizationCommand extends Command
{
    protected $signature = 'app:clear-optimization {--token=}';
    protected $description = 'Clear optimization with token authentication';

    public function handle()
    {
        $token = $this->option('token');

        if ($token !== config('app.artisan_token')) {
            $this->error('Invalid token');
            return 1;
        }

        $commands = [
            'cache:clear',
            'config:clear',
            'route:clear',
            'view:clear',
            'optimize:clear'
        ];

        foreach ($commands as $command) {
            $this->info("Running: {$command}");
            Artisan::call($command);
            $this->line(Artisan::output());
        }

        $this->info('All optimization cleared successfully!');
        return 0;
    }
}
