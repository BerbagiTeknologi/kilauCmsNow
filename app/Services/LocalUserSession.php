<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;

class LocalUserSession
{
    public function put(Request $request, User $user): void
    {
        $role = $user->role ?: 'user';
        $externalUserId = $user->global_user_id ?: null;

        $request->session()->forget([
            'sso_access_token',
            'sso_id_token',
            'sso_payload',
            'user_token',
            'user_payload',
        ]);

        $request->session()->put([
            'user_id' => $externalUserId,
            'user_sub' => $user->global_user_id ?: $user->sso_sub,
            'global_user_id' => $user->global_user_id,
            'local_user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'user_role' => $role,
            'user_level' => $role === 'admin' ? 'admin' : 'donatur',
            'user_photo' => null,
        ]);
    }
}
