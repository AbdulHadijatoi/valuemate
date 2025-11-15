<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestScheduledTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:scheduled-task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test scheduled task execution';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Scheduled task executed successfully at ' . now());
        Log::info('Scheduled task executed at ' . now());
        
        return Command::SUCCESS;
    }
}

