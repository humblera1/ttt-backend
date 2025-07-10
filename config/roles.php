<?php

use App\Enums\Action;
use App\Enums\Entity;
use App\Enums\Role;

return [
    /**
     * List of permissions for "Admin" role
     */
    Role::Admin->name => [
        Entity::Permission->name => [
            Action::Suggest,
            Action::Create,
            Action::View,
            Action::Edit,
            Action::Delete,
        ],
        Entity::Role->name => [
            Action::Suggest,
            Action::Create,
            Action::View,
            Action::Edit,
            Action::Delete,
        ],
        Entity::Position->name => [
            Action::Suggest,
            Action::Create,
            Action::View,
            Action::Edit,
            Action::Delete,
            Action::Approve,
            Action::Reject,
        ],
        Entity::Company->name => [
            Action::Create,
            Action::View,
            Action::Edit,
            Action::Delete,
            Action::Approve,
            Action::Reject,
        ],
        Entity::Tag->name => [
            Action::Create,
            Action::View,
            Action::Edit,
            Action::Delete,
            Action::Approve,
            Action::Reject,
        ],
        Entity::Grade->name => [
            Action::Create,
            Action::View,
            Action::Edit,
            Action::Delete,
        ],
        Entity::User->name => [
            Action::View,
            Action::ViewOwn,
            Action::Edit,
            Action::EditOwn,
            Action::Delete,
            Action::DeleteOwn,
            Action::Ban,
            Action::Unban,
        ],
        Entity::Question->name => [
            Action::Create,
            Action::View,
            Action::ViewPremium,
            Action::Edit,
            Action::EditOwn,
            Action::Delete,
            Action::DeleteOwn,
            Action::Favorite,
            Action::Vote,
            Action::MarkAsSeen,
            Action::Approve,
            Action::Reject,
        ],
        Entity::Suggestion->name => [
            Action::Create,
            Action::View,
            Action::ViewOwn,
            Action::Edit,
            Action::EditOwn,
            Action::Delete,
            Action::DeleteOwn,
            Action::Comment,
            Action::Approve,
            Action::Reject,
        ],
        Entity::QuestionCompanySuggestion->name => [
            Action::Approve,
            Action::Reject,
        ],
        Entity::QuestionPositionSuggestion->name => [
            Action::Approve,
            Action::Reject,
        ],
        Entity::Subscription->name => [
            Action::Subscribe,
            Action::Pay,
            Action::Renew,
            Action::Cancel,
            Action::Activate,
            Action::Deactivate,
            Action::Extend,
            Action::Delete,
            Action::View,
        ],
        Entity::Payment->name => [
            Action::View,
            Action::ViewOwn,
            Action::Refund,
            Action::Delete,
        ],
        Entity::Notification->name => [
            Action::Create,
            Action::View,
            Action::ViewOwn,
            Action::Edit,
            Action::Delete,
            Action::DeleteOwn,
            Action::Send,
            Action::MarkAsRead,
            Action::MarkAsUnread,
        ],
        Entity::Comment->name => [
            Action::Create,
            Action::View,
            Action::Edit,
            Action::EditOwn,
            Action::Delete,
            Action::DeleteOwn,
            Action::Vote,
            Action::Reply,
            Action::ViewDeleted,
        ],
    ],

    /**
     * List of permissions for "Moderator" role
     */
    Role::Moderator->name => [
        Entity::Position->name => [
            Action::Suggest,
            Action::View,
            Action::Edit,
            Action::Approve,
            Action::Reject,
        ],
        Entity::Company->name => [
            Action::Suggest,
            Action::View,
            Action::Edit,
            Action::Approve,
            Action::Reject,
        ],
        Entity::Tag->name => [
            Action::Suggest,
            Action::View,
            Action::Edit,
            Action::Approve,
            Action::Reject,
        ],
        Entity::Grade->name => [
            Action::View,
        ],
        Entity::User->name => [
            Action::View,
            Action::ViewOwn,
            Action::EditOwn,
            Action::DeleteOwn,
            Action::Ban,
            Action::Unban,
        ],
        Entity::Question->name => [
            Action::View,
            Action::ViewPremium,
            Action::Edit,
            Action::EditOwn,
            Action::DeleteOwn,
            Action::Favorite,
            Action::Vote,
            Action::MarkAsSeen,
            Action::Approve,
            Action::Reject,
        ],
        Entity::Suggestion->name => [
            Action::Create,
            Action::View,
            Action::ViewOwn,
            Action::Edit,
            Action::DeleteOwn,
            Action::Comment,
            Action::Approve,
            Action::Reject,
        ],
        Entity::QuestionCompanySuggestion->name => [
            Action::Approve,
            Action::Reject,
        ],
        Entity::QuestionPositionSuggestion->name => [
            Action::Approve,
            Action::Reject,
        ],
        Entity::Subscription->name => [
            Action::Subscribe,
            Action::Pay,
            Action::Renew,
            Action::Cancel,
            Action::View,
        ],
        Entity::Payment->name => [
            Action::ViewOwn,
            Action::Refund,
        ],
        Entity::Notification->name => [
            Action::Create,
            Action::View,
            Action::ViewOwn,
            Action::Edit,
            Action::Delete,
            Action::DeleteOwn,
            Action::Send,
            Action::MarkAsRead,
            Action::MarkAsUnread,
        ],
        Entity::Comment->name => [
            Action::Create,
            Action::View,
            Action::EditOwn,
            Action::Delete,
            Action::DeleteOwn,
            Action::Vote,
            Action::Reply,
            Action::ViewDeleted,
        ],
    ],

    /**
     * List of permissions for "Premium User" role
     */
    Role::PremiumUser->name => [
        Entity::Position->name => [
            Action::Suggest,
            Action::View,
        ],
        Entity::Company->name => [
            Action::Suggest,
            Action::View,
        ],
        Entity::Tag->name => [
            Action::Suggest,
            Action::View,
        ],
        Entity::Grade->name => [
            Action::View,
        ],
        Entity::User->name => [
            Action::View,
            Action::ViewOwn,
            Action::EditOwn,
            Action::DeleteOwn,
        ],
        Entity::Question->name => [
            Action::View,
            Action::ViewPremium,
            Action::Favorite,
            Action::Vote,
            Action::MarkAsSeen,
        ],
        Entity::Suggestion->name => [
            Action::Create,
            Action::ViewOwn,
        ],
        Entity::Subscription->name => [
            Action::Subscribe,
            Action::Pay,
            Action::Renew,
            Action::Cancel,
            Action::ViewOwn,
        ],
        Entity::Payment->name => [
            Action::ViewOwn,
            Action::Refund,
        ],
        Entity::Notification->name => [
            Action::ViewOwn,
            Action::DeleteOwn,
            Action::MarkAsRead,
            Action::MarkAsUnread,
        ],
        Entity::Comment->name => [
            Action::Create,
            Action::View,
            Action::EditOwn,
            Action::DeleteOwn,
            Action::Vote,
            Action::Reply,
        ],
    ],

    /**
     * List of permissions for "User" role
     */
    Role::User->name => [
        Entity::Position->name => [
            Action::Suggest,
            Action::View,
        ],
        Entity::Company->name => [
            Action::Suggest,
        ],
        Entity::Tag->name => [
            Action::Suggest,
            Action::View,
        ],
        Entity::Grade->name => [
            Action::View,
        ],
        Entity::User->name => [
            Action::View,
            Action::ViewOwn,
            Action::EditOwn,
            Action::DeleteOwn,
        ],
        Entity::Question->name => [
            Action::View,
            Action::Favorite,
            Action::Vote,
            Action::MarkAsSeen,
        ],
        Entity::Suggestion->name => [
            Action::Create,
            Action::ViewOwn,
        ],
        Entity::Subscription->name => [
            Action::Subscribe,
            Action::Pay,
            Action::Renew,
            Action::Cancel,
            Action::ViewOwn,
        ],
        Entity::Payment->name => [
            Action::ViewOwn,
            Action::Refund,
        ],
        Entity::Notification->name => [
            Action::ViewOwn,
            Action::DeleteOwn,
            Action::MarkAsRead,
            Action::MarkAsUnread,
        ],
        Entity::Comment->name => [
            Action::Create,
            Action::View,
            Action::EditOwn,
            Action::DeleteOwn,
            Action::Vote,
            Action::Reply,
        ],
    ],

    /**
     * List of permissions for "Guest" role
     */
    Role::Guest->name => [
        Entity::Position->name => [
            Action::View,
        ],
        Entity::Tag->name => [
            Action::View,
        ],
        Entity::Grade->name => [
            Action::View,
        ],
        Entity::User->name => [
            Action::View,
            Action::ViewOwn,
        ],
        Entity::Question->name => [
            Action::View,
            Action::MarkAsSeen,
        ],
        Entity::Notification->name => [
            Action::ViewOwn,
            Action::DeleteOwn,
            Action::MarkAsRead,
            Action::MarkAsUnread,
        ],
        Entity::Comment->name => [
            Action::View,
        ],
    ],
];
