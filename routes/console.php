<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:cleanup-user-notifications')->daily();
Schedule::command('views:flush')->everyMinute()->withoutOverlapping();
