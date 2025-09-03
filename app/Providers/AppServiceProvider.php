<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request; // ← Illuminate の Request は Symfony を継承

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // 1) プロキシを信頼（全許可：0.0.0.0/0 と ::/0）
        Request::setTrustedProxies(
            ['0.0.0.0/0', '::/0'],
            Request::HEADER_X_FORWARDED_ALL
        );

        // 2)（任意だが保険）本番は https を強制
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
