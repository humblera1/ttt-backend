<?php

namespace App\Enums;

enum Action
{
    case Create;
    case ViewOwn;
    case ViewAny;
    case ViewPremium;
    case ViewDeleted;

    // Например, просмотр закрытых профилей
//    case ViewClosed;
    case EditOwn;
    case EditAny;
    case DeleteOwn;
    case DeleteAny;
    case DeleteBulk;
    case ForceDeleteOwn;
    case ForceDeleteAny;
    case ForceDeleteBulk;
    case RestoreOwn;
    case RestoreAny;
    case Approve;
    case Reject;
    case ChangeStatus;
    case Ban;

    //    case BanAny;
    case BanBulk;
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
    case Assign;
    case Propose;
}
