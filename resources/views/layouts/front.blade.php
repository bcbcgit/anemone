<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="robots" content="noindex, nofollow">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ログイン')</title>

    {{-- Tailwind ビルド（Vite） --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- 任意：アイコン等が必要なら --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 text-slate-900 antialiased">

<div class="flex min-h-screen items-center justify-center p-6">
    <div class="md:max-w-[400px]">
        <div class="rounded-2xl border border-slate-200 bg-white/90 shadow-xl backdrop-blur-sm">
            <div class="p-6 sm:p-8">
                @yield('content')
            </div>
        </div>
    </div>
</div>
</body>
</html>
