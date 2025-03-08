<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('app:reset')]
class ResetCommand extends Command
{
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
