<?php

namespace App\Enums\Settings;

enum Section: string
{
    case Question = 'question';
    case Tag = 'tag';
    case Company = 'company';
    case Statistics = 'statistics';
    case Notifications = 'notifications';
    case Comments = 'comments';
}
