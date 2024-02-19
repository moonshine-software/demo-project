<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ResetCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-console';

    public function handle(): int
    {
        $this->call('migrate:fresh', [
            '--force' => true,
            '--seed' => true
        ]);

        File::cleanDirectory(storage_path('app/public'));

        return self::SUCCESS;
    }
}
