<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schedule;

Schedule::command('demo:cleanup --limit=100')->everyFiveMinutes()->withoutOverlapping();
