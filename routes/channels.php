<?php

use app\Models\User;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/
/*

Broadcast::channel('private-chat.', function ($user) {
    return Auth::check();
});

Broadcast::routes(['middleware' => ['auth:api']]);


Broadcast::channel('private-chat.{id}', function ($user, $userId) {
    return $user->id === $userId;
  });

Broadcast::channel('private-chat.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

*/
Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('channel-chat', function ($user) {
    return $user;
});

Broadcast::channel('channel-direct.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

