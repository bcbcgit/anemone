<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest; // ← これを使う

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 全プロキシを信頼（必要に応じてCIDRに絞ってOK）
        SymfonyRequest::setTrustedProxies(
            ['0.0.0.0/0', '::/0'],
            SymfonyRequest::HEADER_X_FORWARDED_ALL // ← Symfony の定数
        );

        // 保険として本番は https を強制
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
