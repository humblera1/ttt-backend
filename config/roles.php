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
            Action::ForceDeleteAny,
            Action::ForceDeleteBulk,
            Action::RestoreAny,
            Action::ChangeStatus,
        ],
        Entity::Company->name => [
            Action::Create,
            Action::ViewAny,
            Action::EditAny,
            Action::DeleteAny,
            Action::DeleteBulk,
            Action::ForceDeleteAny,
            Action::ForceDeleteBulk,
            Action::RestoreAny,
            Action::ChangeStatus,
        ],
        Entity::Tag->name => [
            Action::Create,
            Action::ViewAny,
            Action::EditAny,
            Action::DeleteAny,
            Action::DeleteBulk,
            Action::ForceDeleteAny,
            Action::ForceDeleteBulk,
            Action::RestoreAny,
            Action::ChangeStatus,
        ],
        Entity::Grade->name => [
            Action::Create,
            Action::ViewAny,
            Action::EditAny,
            Action::DeleteAny,
            Action::DeleteBulk,
            Action::RestoreAny,
        ],
        Entity::User->name => [
            Action::ViewOwn,
            Action::ViewAny,
            Action::EditOwn,
            Action::EditAny,
            Action::DeleteOwn,
            Action::DeleteAny,
            Action::DeleteBulk,
            Action::Ban,
            Action::BanBulk,
            Action::Unban,
            Action::RestoreAny,
        ],
        Entity::Question->name => [
            Action::Create,
            Action::ViewOwn,
            Action::ViewAny,
            Action::ViewPremium,
            Action::EditOwn,
            Action::EditAny,
            Action::DeleteOwn,
            Action::DeleteAny,
            Action::ForceDeleteOwn,
            Action::ForceDeleteAny,
            Action::DeleteBulk,
            Action::ForceDeleteBulk,
            Action::RestoreOwn,
            Action::RestoreAny,
            Action::Favorite,
            Action::Vote,
            Action::MarkAsSeen,
            Action::ChangeStatus,
            Action::Propose,
            Action::SendFeedback,
        ],
        Entity::Suggestion->name => [
            Action::Create,
            Action::ViewAny,
            Action::EditAny,
            Action::DeleteAny,
            Action::DeleteBulk,
            Action::Comment,
            Action::ChangeStatus,
        ],
        Entity::QuestionCompanySuggestion->name => [
            Action::ChangeStatus,
        ],
        Entity::QuestionPositionSuggestion->name => [
            Action::ChangeStatus,
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
        Entity::Setting->name => [
            Action::ViewAny,
        ],
        Entity::Statistic->name => [
            Action::ViewAny,
            Action::DeleteAny,
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
            Action::ChangeStatus,
        ],
        Entity::Company->name => [
            Action::Suggest,
            Action::ViewAny,
            Action::EditAny,
//            Action::DeleteAny,
            Action::ChangeStatus,
        ],
        Entity::Tag->name => [
            Action::Suggest,
            Action::ViewAny,
            Action::EditAny,
//            Action::DeleteAny,
            Action::ChangeStatus,
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
            Action::RestoreOwn,
        ],
        Entity::Question->name => [
            Action::ViewAny,
            Action::ViewPremium,
            Action::EditAny,
//            Action::DeleteAny,
            Action::Favorite,
            Action::Vote,
            Action::MarkAsSeen,
            Action::ChangeStatus,
            Action::Propose,
            Action::SendFeedback,
        ],
        Entity::Suggestion->name => [
            Action::Create,
            Action::ViewAny,
            Action::EditAny,
            Action::DeleteAny,
            Action::Comment,
            Action::ChangeStatus,
        ],
        Entity::QuestionCompanySuggestion->name => [
            Action::ChangeStatus,
        ],
        Entity::QuestionPositionSuggestion->name => [
            Action::ChangeStatus,
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
        Entity::Statistic->name => [
            Action::ViewAny,
            Action::DeleteAny,
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
            Action::RestoreOwn,
        ],
        Entity::Question->name => [
            Action::ViewAny,
            Action::ViewPremium,
            Action::Favorite,
            Action::Vote,
            Action::MarkAsSeen,
            Action::Propose,
            Action::SendFeedback,
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
            Action::RestoreOwn,
        ],
        Entity::Question->name => [
            Action::ViewOwn,
            Action::ViewAny,
            Action::Favorite,
            Action::Vote,
            Action::MarkAsSeen,
            Action::Propose,
            Action::SendFeedback,
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
            Action::Propose,
            Action::SendFeedback,
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
