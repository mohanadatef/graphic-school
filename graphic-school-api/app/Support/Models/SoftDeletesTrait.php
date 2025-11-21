<?php

namespace App\Support\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

trait SoftDeletesTrait
{
    use SoftDeletes;

    /**
     * Get the name of the "deleted at" column.
     */
    public function getDeletedAtColumn(): string
    {
        return 'deleted_at';
    }
}

