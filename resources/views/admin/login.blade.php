@extends('layouts.front')

@section('title', 'ログイン')
@section('page_title', 'ログイン画面')

@section('content')
    <div class="mx-auto w-full px-4 sm:px-0"> {{-- ★ 追加：中央寄せ & PCで最大400px --}}

        {{-- ヘッダ / ブランド --}}
        <div class="mb-6 flex items-center gap-3">
            <div class="grid h-10 w-10 place-items-center rounded-xl bg-slate-900 text-white shadow-sm">
                <span class="text-sm font-bold">U</span>
            </div>
            <div>
                <h1 class="text-xl font-bold leading-tight">ログイン</h1>
                <p class="text-xs text-slate-500">メールアドレスとパスワードを入力してください。</p>
            </div>
        </div>

        <!-- ステータス / エラー（必要に応じて display:block に） -->
        <div id="statusBox" class="mb-4 hidden rounded-md border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-700">
            サンプル：ステータスメッセージ
        </div>
        <div id="errorBox" class="mb-4 hidden rounded-md border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700">
            <p class="font-medium">入力内容を確認してください。</p>
            <ul class="ml-5 list-disc">
                <li>メールアドレスを入力してください。</li>
                <li>パスワードを入力してください。</li>
            </ul>
        </div>

        <!-- フォーム（完全静的。action は # に） -->
        <form action="{{ route('login') }}" method="post" novalidate class="space-y-4">
            @csrf
            <!-- メールアドレス -->
            <div>
                <label for="email" class="block text-sm font-medium text-slate-700">メールアドレス</label>
                <input id="email" type="email" name="email" required autocomplete="email" autofocus
                       placeholder="you@example.com"
                       class="mt-1 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder-slate-400 shadow-sm outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900" />
                <div id="emailHelp" class="mt-1 hidden text-sm text-rose-600">メールアドレスが未入力です。</div>
            </div>

            <!-- パスワード -->
            <div>
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-sm font-medium text-slate-700">パスワード</label>
                    <button type="button" id="togglePwd" class="text-xs text-slate-600 hover:text-slate-900">表示</button>
                </div>
                <div class="relative">
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                           placeholder="••••••••"
                           class="mt-1 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 pr-16 text-slate-900 placeholder-slate-400 shadow-sm outline-none focus:border-slate-900 focus:ring-1 focus:ring-slate-900" />
                    <button type="button" id="togglePwdIcon"
                            class="absolute inset-y-0 right-2 my-1 hidden items-center rounded-md border border-slate-300 bg-white px-2 text-xs text-slate-600 hover:text-slate-900 sm:flex">
                        表示
                    </button>
                </div>
                <div id="passwordHelp" class="mt-1 hidden text-sm text-rose-600">パスワードが未入力です。</div>
            </div>

            <!-- 操作行 -->
            <div class="mt-6 flex items-center justify-between">
                <button type="submit"
                        class="inline-flex items-center justify-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white shadow hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:ring-offset-2">
                    ログイン
                </button>
            </div>
        </form>

    </div> {{-- ★ 追加したラッパーの閉じタグ --}}
@endsection
