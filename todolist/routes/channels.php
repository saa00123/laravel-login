<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('user', function ($user) {
    return Auth::check();
});

Broadcast::channel('my-channel', function ($user) {
    return Auth::check();
});
