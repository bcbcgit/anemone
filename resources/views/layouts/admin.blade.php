<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8" />
    <meta name="robots" content="noindex, nofollow">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', '管理画面')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link rel="stylesheet" href="/css/common.css">

    <meta name="theme-color" content="#0f172a">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-slate-50 text-slate-800 antialiased"
      x-data="{ sidebarOpen: false, userMenuOpen: false }" @keydown.window.escape="sidebarOpen=false; userMenuOpen=false">

<!-- ヘッダ -->
<header class="fixed top-0 inset-x-0 z-50 bg-white/95 backdrop-blur border-b border-slate-200 h-16">
    <div class="mx-auto max-w-screen px-4 h-full">
        <div class="flex items-center justify-between h-16">
            <!-- ロゴ -->
            <a href="{{ route('scenarios.index') }}" class="flex items-center gap-2 min-w-0">
                <div class="w-8 h-8 rounded-md bg-slate-900"></div>
                <span class="font-semibold tracking-wide text-slate-900">AneMone</span>
            </a>

            <!-- 右側 -->
            <div class="flex items-center gap-2">
                <!-- デスクトップユーザー -->
                <div class="hidden lg:block relative">
                    <button type="button"
                            class="flex items-center gap-2 rounded-md px-2 py-1 hover:bg-slate-100 focus:outline-none"
                            @click="userMenuOpen = !userMenuOpen" aria-haspopup="true" :aria-expanded="userMenuOpen">
                        <span class="sr-only">ユーザーメニューを開く</span>
                        <div class="text-right hidden md:block">
                            <p class="text-sm leading-4 font-medium text-slate-900 truncate">テストユーザー</p>
                            <p class="text-xs text-slate-500 -mt-0.5">user@example.com</p>
                        </div>
                        <img src="https://placehold.co/80x80?text=U"
                             alt="avatar" class="w-10 h-10 rounded-full ring-1 ring-slate-200">
                    </button>

                    <!-- ドロップダウン -->
                    <div x-show="userMenuOpen" x-transition.origin.top.right
                         @click.outside="userMenuOpen=false"
                         class="absolute right-0 mt-2 w-48 rounded-md border border-slate-200 bg-white shadow-lg overflow-hidden"
                         style="display:none">
                        <a href="#" class="block px-4 py-2 text-sm hover:bg-slate-50">情報</a>
                        <form action="{{ route('admin.logout') }}" method="POST">
                            @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm hover:bg-slate-50">ログアウト</button>
                        </form>
                    </div>
                </div>

                <!-- SP: ハンバーガー -->
                <button type="button"
                        class="lg:hidden inline-flex items-center justify-center w-10 h-10 rounded-md hover:bg-slate-100"
                        @click="sidebarOpen = !sidebarOpen" :aria-expanded="sidebarOpen" aria-controls="sidebar">
                    <span class="sr-only">メニューを開閉</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</header>

<!-- サイドバー背景 -->
<div class="fixed inset-0 z-40 bg-black/30 lg:hidden" x-show="sidebarOpen" x-transition.opacity style="display:none" @click="sidebarOpen=false"></div>

<!-- サイドバー -->
<aside id="sidebar"
       class="fixed left-0 top-16 bottom-0 z-40 w-72 bg-slate-900 text-slate-100 border-r border-slate-800
                transform transition-transform duration-200 ease-out
                lg:translate-x-0"
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
    <nav class="h-full overflow-y-auto sidebar-scroll">
        <ul class="py-3 px-3 space-y-1">
            <li>
                <a href="{{ route('scenarios.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-slate-800">
                    <i class="fa-solid fa-gauge text-slate-300 w-5"></i>
                    シナリオ一覧
                </a>
            </li>
            <li>
                <a href="{{ route('scenarios.create') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-slate-800">
                    <i class="fa-solid fa-users text-slate-300 w-5"></i>
                    シナリオ登録
                </a>
            </li>
            <li>
                <a href="{{ route('kinds.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-slate-800">
                    <i class="fa-solid fa-users text-slate-300 w-5"></i>
                    シナリオ種別管理
                </a>
            </li>
            <li>
                <a href="{{ route('elements.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-slate-800">
                    <i class="fa-solid fa-users text-slate-300 w-5"></i>
                    シナリオ要素管理
                </a>
            </li>
            <li>
                <a href="{{ route('characters.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-slate-800">
                    <i class="fa-solid fa-users text-slate-300 w-5"></i>
                    クリチケ管理
                </a>
            </li>
            <li>
                <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-slate-800">
                    <i class="fa-solid fa-gear text-slate-300 w-5"></i>
                    設定
                </a>
            </li>
        </ul>

    </nav>
</aside>

<!-- メイン -->
<main class="pt-16 lg:pl-72 min-h-screen">
    <div class="mx-auto max-w-screen-2xl px-4 py-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-slate-900">@yield('page_title', 'ダッシュボード')</h1>
                <p class="text-sm text-slate-500 mt-1">@yield('page_desc')</p>
            </div>
            <div class="hidden lg:flex gap-2">
            </div>
        </div>

        <div class="mt-6">
            @yield('content')
        </div>
    </div>
</main>
</body>
</html>
