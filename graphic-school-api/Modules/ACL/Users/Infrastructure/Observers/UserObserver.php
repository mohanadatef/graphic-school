<?php

namespace Modules\ACL\Users\Infrastructure\Observers;

use App\Support\Observers\BaseObserver;
use Modules\ACL\Users\Models\User;

class UserObserver extends BaseObserver
{
    // BaseObserver handles: created, updated, deleted, restored, forceDeleted
    // Override if you need custom behavior
}

