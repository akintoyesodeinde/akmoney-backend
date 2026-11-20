<?php

namespace App\Support;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ModelNotifier
{
    public static function notify(
        Model $model,
        string $action,
        ?string $title = null,
        ?string $message = null,
        array $meta = []
    ): void {
        // Resolve the user this notification belongs to (not the actor).
        // For User models the model itself is the recipient.
        // For related models (SendMoney, SupportTicket, etc.) use their user_id.
        // Fall back to Auth::id() only when no better target can be found.
        if ($model instanceof \App\Models\User) {
            $userId = $model->getKey();
        } elseif (isset($model->user_id)) {
            $userId = $model->user_id;
        } else {
            $userId = Auth::id();
        }

        Notification::create([
            'user_id'    => $userId,
            'model_type' => get_class($model),
            'model_id'   => $model->getKey(),
            'action'     => $action,
            'title'      => $title ?? class_basename($model) . " {$action}",
            'message'    => $message,
            'meta'       => $meta,
        ]);
    }
}
