<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:cleanup-user-notifications')->daily();
