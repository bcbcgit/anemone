{{-- resources/views/admin/users/create.blade.php --}}
@extends('layouts.admin')

@section('title', 'ユーザー新規作成')
@section('page_title', 'ユーザー新規作成')

@section('content')
    <form action="{{ route('admin.users.store') }}" method="post" enctype="multipart/form-data" class="space-y-6 max-w-xl">
        @csrf

        @if (session('status'))
            <div class="p-3 border border-emerald-300 bg-emerald-50 rounded text-emerald-700 text-sm">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="p-3 border border-red-300 bg-red-50 rounded text-red-700 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-md border border-slate-200 bg-white p-4 shadow-sm space-y-4">
            {{-- 名前（必須） --}}
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700">名前 <span class="text-rose-600">*</span></label>
                <input id="name" type="text" name="name" class="form-input mt-1 w-full" value="{{ old('name') }}" required maxlength="100" placeholder="山田 太郎">
                @error('name') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            {{-- メールアドレス（必須） --}}
            <div>
                <label for="email" class="block text-sm font-medium text-slate-700">メールアドレス <span class="text-rose-600">*</span></label>
                <input id="email" type="email" name="email" class="form-input mt-1 w-full" value="{{ old('email') }}" required placeholder="you@example.com" autocomplete="email">
                @error('email') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            {{-- パスワード（必須） --}}
            <div>
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-sm font-medium text-slate-700">パスワード <span class="text-rose-600">*</span></label>
                    <button type="button" id="togglePwd" class="text-xs text-slate-600 hover:text-slate-800">表示</button>
                </div>
                <input id="password" type="password" name="password" class="form-input mt-1 w-full" required placeholder="8文字以上を推奨" autocomplete="new-password">
                @error('password') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                <p class="mt-1 text-xs text-slate-500">英数字・記号の組み合わせを推奨</p>
            </div>

            {{-- パスワード確認（必須・一致チェック用） --}}
            <div>
                <div class="flex items-center justify-between">
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700">パスワード（確認） <span class="text-rose-600">*</span></label>
                    <button type="button" id="togglePwd2" class="text-xs text-slate-600 hover:text-slate-800">表示</button>
                </div>
                <input id="password_confirmation" type="password" name="password_confirmation" class="form-input mt-1 w-full" required autocomplete="new-password">
                {{-- バリデーションで不一致時は password にエラーが乗る想定（confirmed ルール） --}}
            </div>

            {{-- 画像（必須） --}}
            <div>
                <label for="image" class="block text-sm font-medium text-slate-700">画像 <span class="text-rose-600">*</span></label>
                <input id="image" type="file" name="image" accept="image/*" class="form-input mt-1 w-full" required>
                @error('image') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                <p class="mt-1 text-xs text-slate-500">JPG/PNG/WebP など、5MB 以下を推奨</p>
            </div>
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ url('/admin') }}" class="rounded-md border border-slate-200 bg-white px-4 py-2 text-sm hover:bg-slate-50">キャンセル</a>
            <button class="rounded-md bg-slate-900 text-white px-4 py-2 text-sm hover:bg-slate-800">作成する</button>
        </div>
    </form>

    <script>
        // パスワード表示切替
        (function(){
            const bindToggle = (btnId, inputId) => {
                const btn = document.getElementById(btnId);
                const inp = document.getElementById(inputId);
                btn?.addEventListener('click', () => {
                    if (!inp) return;
                    const show = inp.type === 'password';
                    inp.type = show ? 'text' : 'password';
                    btn.textContent = show ? '非表示' : '表示';
                });
            };
            bindToggle('togglePwd', 'password');
            bindToggle('togglePwd2', 'password_confirmation');
        })();
    </script>
@endsection
