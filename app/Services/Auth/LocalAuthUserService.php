<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class LocalAuthUserService
{
    public function syncFromAuthService(array $authUser, ?array $mappingLookup = null): User
    {
        return DB::transaction(function () use ($authUser, $mappingLookup): User {
            $user = $this->findLocalUser($authUser);
            $mapping = $this->primaryMapping($mappingLookup);
            $metadata = $mapping['metadata'] ?? [];
            $globalUserId = $this->globalUserId($authUser);

            if (!$user) {
                $user = new User;
                $user->email = Str::lower((string) $authUser['email']);
                $user->password = Hash::make(Str::random(48));
                $user->role = $metadata['role'] ?? config('auth_service.default_role', 'user');
            }

            $user->name = $authUser['name'] ?? $user->name;
            $user->email = Str::lower((string) ($authUser['email'] ?? $user->email));

            if (Schema::hasColumn('users', 'global_user_id')) {
                $user->global_user_id = $globalUserId ?: $user->global_user_id;
            }

            if (!$user->role) {
                $user->role = $metadata['role'] ?? config('auth_service.default_role', 'user');
            }

            $user->sso_payload = [
                'provider' => 'kilau_auth',
                'global_user' => $authUser,
                'mapping' => $mapping,
            ];
            $user->last_login_at = now();
            $user->save();

            return $user;
        });
    }

    public function findLocalUser(array $authUser): ?User
    {
        $globalUserId = $this->globalUserId($authUser);
        $email = Str::lower((string) ($authUser['email'] ?? ''));

        if ($globalUserId !== '' && Schema::hasColumn('users', 'global_user_id')) {
            $user = User::query()->where('global_user_id', $globalUserId)->first();

            if ($user) {
                return $user;
            }
        }

        if ($email === '') {
            return null;
        }

        return User::query()->whereRaw('LOWER(email) = ?', [$email])->first();
    }

    private function globalUserId(array $authUser): string
    {
        return trim((string) ($authUser['global_user_id'] ?? $authUser['id'] ?? ''));
    }

    private function primaryMapping(?array $mappingLookup): ?array
    {
        $mappings = $mappingLookup['mappings'] ?? [];

        if (!is_array($mappings) || $mappings === []) {
            return null;
        }

        $appName = config('auth_service.app_name', 'kilauCms');

        foreach ($mappings as $mapping) {
            if (($mapping['app_name'] ?? null) === $appName) {
                return $mapping;
            }
        }

        return $mappings[0];
    }
}
