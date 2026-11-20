<?php

namespace App\Traits;

use App\Support\ModelNotifier;

trait NotifiesChanges
{
    protected static function bootNotifiesChanges()
    {
        static::created(function ($model) {
            ModelNotifier::notify($model, 'created');
        });

        static::updated(function ($model) {
            // Skip notification for purely internal / preference fields
            $internalOnly = [
                'current_mode', 'remember_token', 'updated_at',
                'pin_failed_attempts', 'pin_locked_until',
                'profile_change_code', 'profile_change_code_sent_at',
                'pending_email', 'pending_mobile',
            ];
            $meaningful = array_diff_key($model->getChanges(), array_flip($internalOnly));
            if (empty($meaningful)) {
                return;
            }
            ModelNotifier::notify($model, 'updated', null, null, [
                'changes' => $meaningful,
            ]);
        });
    }
}
