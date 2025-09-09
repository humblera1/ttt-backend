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
//            Action::Suggest,
            Action::Create,
            Action::ViewAny,
            Action::EditAny,
            Action::DeleteAny,
            Action::DeleteBulk,
        ],
        Entity::Role->name => [
//            Action::Suggest,
            Action::Create,
            Action::ViewAny,
            Action::EditAny,
            Action::DeleteAny,
            Action::DeleteBulk,
            Action::Assign,
        ],
        Entity::Position->name => [
            Action::Suggest,
            Action::Create,
            Action::ViewAny,
            Action::EditAny,
            Action::DeleteAny,
            Action::DeleteBulk,
            Action::Approve,
            Action::Reject,
        ],
        Entity::Company->name => [
            Action::Create,
            Action::ViewAny,
            Action::EditAny,
            Action::DeleteAny,
            Action::DeleteBulk,
            Action::Approve,
            Action::Reject,
        ],
        Entity::Tag->name => [
            Action::Create,
            Action::ViewAny,
            Action::EditAny,
            Action::DeleteAny,
            Action::DeleteBulk,
            Action::Approve,
            Action::Reject,
        ],
        Entity::Grade->name => [
            Action::Create,
            Action::ViewAny,
            Action::EditAny,
            Action::DeleteAny,
            Action::DeleteBulk,
        ],
        Entity::User->name => [
            Action::ViewOwn,
            Action::ViewAny,
            Action::EditAny,
            Action::DeleteAny,
            Action::DeleteBulk,
            Action::Ban,
            Action::BanBulk,
            Action::Unban,
        ],
        Entity::Question->name => [
            Action::Create,
            Action::ViewAny,
            Action::ViewPremium,
            Action::EditAny,
            Action::DeleteAny,
            Action::DeleteBulk,
            Action::Favorite,
            Action::Vote,
            Action::MarkAsSeen,
            Action::Approve,
            Action::Reject,
        ],
        Entity::Suggestion->name => [
            Action::Create,
            Action::ViewAny,
            Action::EditAny,
            Action::DeleteAny,
            Action::DeleteBulk,
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
            Action::ViewAny,
            Action::EditAny,
            Action::DeleteAny,
            Action::DeleteBulk,
            Action::Subscribe,
            Action::Pay,
            Action::Renew,
            Action::Cancel,
            Action::Activate,
            Action::Deactivate,
            Action::Extend,
        ],
        Entity::Payment->name => [
            Action::ViewAny,
            Action::EditAny,
            Action::DeleteAny,
            Action::DeleteBulk,
            Action::Refund,
        ],
        Entity::Notification->name => [
            Action::Create,
            Action::ViewAny,
            Action::EditAny,
            Action::DeleteAny,
            Action::DeleteBulk,
            Action::Send,
            Action::MarkAsRead,
            Action::MarkAsUnread,
        ],
        Entity::Comment->name => [
            Action::Create,
            Action::ViewAny,
            Action::EditAny,
            Action::DeleteAny,
            Action::DeleteBulk,
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
            Action::ViewAny,
            Action::EditAny,
//            Action::DeleteAny,
            Action::Approve,
            Action::Reject,
        ],
        Entity::Company->name => [
            Action::Suggest,
            Action::ViewAny,
            Action::EditAny,
//            Action::DeleteAny,
            Action::Approve,
            Action::Reject,
        ],
        Entity::Tag->name => [
            Action::Suggest,
            Action::ViewAny,
            Action::EditAny,
//            Action::DeleteAny,
            Action::Approve,
            Action::Reject,
        ],
        Entity::Grade->name => [
            Action::ViewAny,
        ],
        Entity::User->name => [
            Action::ViewOwn,
            Action::ViewAny,
            Action::EditAny,
            Action::DeleteOwn,
            Action::Ban,
            Action::Unban,
        ],
        Entity::Question->name => [
            Action::ViewAny,
            Action::ViewPremium,
            Action::EditAny,
//            Action::DeleteAny,
            Action::Favorite,
            Action::Vote,
            Action::MarkAsSeen,
            Action::Approve,
            Action::Reject,
        ],
        Entity::Suggestion->name => [
            Action::Create,
            Action::ViewAny,
            Action::EditAny,
            Action::DeleteAny,
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
            Action::ViewAny,
        ],
        Entity::Payment->name => [
            Action::ViewOwn,
            Action::Refund,
        ],
        Entity::Notification->name => [
            Action::Create,
            Action::ViewAny,
            Action::DeleteOwn,
            Action::Send,
            Action::MarkAsRead,
            Action::MarkAsUnread,
        ],
        Entity::Comment->name => [
            Action::Create,
            Action::ViewAny,
            Action::EditAny,
            Action::DeleteAny,
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
            Action::ViewAny,
        ],
        Entity::Company->name => [
            Action::Suggest,
            Action::ViewAny,
        ],
        Entity::Tag->name => [
            Action::Suggest,
            Action::ViewAny,
        ],
        Entity::Grade->name => [
            Action::ViewAny,
        ],
        Entity::User->name => [
            Action::ViewOwn,
            Action::ViewAny,
            Action::EditOwn,
            Action::DeleteOwn,
        ],
        Entity::Question->name => [
            Action::ViewAny,
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
            Action::ViewOwn,
            Action::ViewAny,
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
            Action::ViewAny,
        ],
        Entity::Company->name => [
            Action::Suggest,
        ],
        Entity::Tag->name => [
            Action::Suggest,
            Action::ViewAny,
        ],
        Entity::Grade->name => [
            Action::ViewAny,
        ],
        Entity::User->name => [
            // todo: есть ли необходимость в ViewOwn в системе?
            Action::ViewOwn,
            Action::ViewAny,
            Action::EditOwn,
            Action::DeleteOwn,
        ],
        Entity::Question->name => [
            Action::ViewOwn,
            Action::ViewAny,
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
//            Action::ViewOwn,
            Action::DeleteOwn,
            Action::MarkAsRead,
            Action::MarkAsUnread,
        ],
        Entity::Comment->name => [
            Action::Create,
            Action::ViewOwn,
            Action::ViewAny,
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
            Action::ViewAny,
        ],
        Entity::Tag->name => [
            Action::ViewAny,
        ],
        Entity::Grade->name => [
            Action::ViewAny,
        ],
        Entity::User->name => [
            Action::ViewAny,
        ],
        Entity::Question->name => [
            Action::ViewAny,
            Action::MarkAsSeen,
        ],
        Entity::Notification->name => [
//            Action::ViewOwn,
            Action::DeleteOwn,
            Action::MarkAsRead,
            Action::MarkAsUnread,
        ],
        Entity::Comment->name => [
            Action::ViewAny,
        ],
    ],
];
