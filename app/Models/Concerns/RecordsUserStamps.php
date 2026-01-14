<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait RecordsUserStamps
{
    protected static function bootRecordsUserStamps(): void
    {
        static::creating(function (Model $model): void {
            $adminId = static::getAdminUserId();
            if ($adminId === null) {
                return;
            }

            if ($model->getAttribute('created_user_id') === null) {
                $model->setAttribute('created_user_id', $adminId);
            }
            if ($model->getAttribute('user_created_at') === null) {
                $model->setAttribute('user_created_at', now());
            }
        });

        static::updating(function (Model $model): void {
            if ($model->isDirty('deleted_at') || $model->isDirty('user_deleted_at')) {
                return;
            }

            $adminId = static::getAdminUserId();
            if ($adminId === null) {
                return;
            }

            $model->setAttribute('update_user_id', $adminId);
            $model->setAttribute('user_updated_at', now());
        });

        static::deleting(function (Model $model): void {
            if (method_exists($model, 'isForceDeleting') && $model->isForceDeleting()) {
                return;
            }

            $adminId = static::getAdminUserId();
            if ($adminId === null) {
                return;
            }

            $model->setAttribute('deleted_user_id', $adminId);
            $model->setAttribute('user_deleted_at', now());
        });
    }

    protected static function getAdminUserId(): ?int
    {
        $guard = Auth::guard('admin');
        if (!$guard->check()) {
            return null;
        }

        $adminId = $guard->id();
        return is_numeric($adminId) ? (int) $adminId : null;
    }
}
