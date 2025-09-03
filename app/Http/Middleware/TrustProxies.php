<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    // Traefik直後にnginxが1段の構成なら '*' でOK（厳密にするならDockerネットワークCIDR）
    protected $proxies = '*';

    // X-Forwarded-* を丸ごと信頼
    protected $headers = Request::HEADER_X_FORWARDED_ALL;
}
