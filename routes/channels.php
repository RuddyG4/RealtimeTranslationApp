<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chatUsers.{userId}', function (User $user, int $userId) {
    return (int) $user->id === (int) $userId;
});