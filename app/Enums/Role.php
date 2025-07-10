<?php

namespace App\Enums;

enum Role
{
    case Admin;
    case Moderator;
    case PremiumUser;
    case User;
    case Guest;
}
