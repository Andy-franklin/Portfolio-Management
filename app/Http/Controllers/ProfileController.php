<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Jetstream\Http\Controllers\Inertia\UserProfileController;
use Laravel\Jetstream\Jetstream;

class ProfileController extends UserProfileController
{
    public function show(Request $request)
    {
        return Jetstream::inertia()->render($request, 'Profile/Show', [
            'sessions' => $this->sessions($request)->all(),
            'tokens' => $request->user()->tokens,
            'availablePermissions' => Jetstream::$permissions,
            'defaultPermissions' => Jetstream::$defaultPermissions,
        ]);
    }
}
