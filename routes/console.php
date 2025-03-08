<?php

use App\Console\Commands\ResetCommand;
use Illuminate\Support\Facades\Schedule;

Schedule::command(ResetCommand::class)->everyFifteenMinutes();
