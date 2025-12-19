<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class SsoUserInfoClient
{
    public function fetch(string $accessToken): array
    {
        $cacheKey = 'sso:userinfo:'.sha1($accessToken);
        $ttl = (int) config('sso.cache_ttl', 60);
        $store = config('sso.cache_store');

        $callback = function () use ($accessToken) {
            $request = Http::timeout(config('sso.timeout', 5));

            $accept = config('sso.accept_header');
            $request = $accept
                ? $request->withHeaders(['Accept' => $accept])
                : $request->acceptJson();

            $url = rtrim(config('sso.management_base_url'), '/').'/api/userinfo';

            $response = $request
                ->withToken($accessToken)
                ->get($url);

            if ($response->status() === 401) {
                throw new UnauthorizedHttpException('Bearer', 'Token SSO tidak valid.');
            }

            try {
                $response->throw();
            } catch (RequestException $exception) {
                throw new UnauthorizedHttpException('Bearer', 'Validasi SSO gagal.', $exception);
            }

            return $response->json();
        };

        if ($ttl <= 0) {
            return $callback();
        }

        $cache = $store ? Cache::store($store) : Cache::store();

        return $cache->remember($cacheKey, now()->addSeconds($ttl), $callback);
    }
}
