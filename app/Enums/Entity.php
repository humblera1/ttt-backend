<?php

namespace App\Enums;

enum Entity
{
    case Permission;
    case Role;
    case Position;
    case Company;
    case Tag;
    case Grade;
    case User;
    case Question;
    case Suggestion;
    case QuestionCompanySuggestion;
    case QuestionPositionSuggestion;
    case Subscription;
    case Payment;
    case Notification;
    case Comment;
}
