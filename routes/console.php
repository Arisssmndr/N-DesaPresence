<?php

use Illuminate\Support\Facades\Schedule;
use App\Jobs\MarkAbsentJob;
use App\Jobs\UpdateJamPulangJob;
use App\Jobs\DatabaseBackupJob;

// 1. Mark employees without scan as Alpa at 23:59 daily
Schedule::job(new MarkAbsentJob)->dailyAt('23:59');

// 2. Update jam_pulang every 5 minutes
Schedule::job(new UpdateJamPulangJob)->everyFiveMinutes();

// 3. Automated daily database backup at 22:00
Schedule::job(new DatabaseBackupJob)->dailyAt('22:00');
