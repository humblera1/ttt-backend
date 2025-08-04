<?php

namespace App\Enums;

enum Action
{
    case Create;
    case View;
    case ViewOwn;
    case ViewAny;
    case ViewPremium;
    case ViewDeleted;
    case Edit;
    case EditOwn;
    case Delete;
    case DeleteOwn;
    case DeleteAny;
    case Approve;
    case Reject;
    case Ban;
    case BanAny;
    case Unban;
    case Favorite;
    case Vote;
    case MarkAsSeen;
    case Comment;
    case Subscribe;
    case Pay;
    case Renew;
    case Cancel;
    case Activate;
    case Extend;
    case Deactivate;
    case Refund;
    case MarkAsRead;
    case MarkAsUnread;
    case Send;
    case Reply;
    case Suggest;
}
