{{-- resources/views/elements/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'シナリオ要素 管理')
@section('page_title', 'シナリオ要素 管理')

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">

        @if (session('success'))
            <div class="p-3 rounded border border-emerald-300 bg-emerald-50 text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 rounded border border-red-300 bg-red-50">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li class="!text-red-700 font-medium">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- 登録フォーム（index内） --}}
        <form action="{{ route('elements.store') }}" method="post" class="space-y-4">
            @csrf
            <div class="rounded-md border border-slate-200 bg-white p-4 shadow-sm space-y-3">
                <div>
                    <label class="block text-sm font-medium text-slate-700">
                        シナリオ要素 <span class="text-rose-600">*</span>
                    </label>
                    <input name="title" type="text" maxlength="100" required value="{{ old('title') }}"
                           class="mt-1 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm
                           focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-slate-500">
                    @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                <span class="block text-sm font-medium text-slate-700">
                    表示設定 <span class="text-rose-600">*</span>
                </span>
                    <div class="mt-2 flex items-center gap-6">
                        <label class="inline-flex items-center gap-2">
                            <input type="radio" name="visible" value="1"
                                   @checked(old('visible',1)==1)
                                   class="rounded border-slate-400 text-slate-700 focus:ring-slate-500">
                            <span>表示</span>
                        </label>
                        <label class="inline-flex items-center gap-2">
                            <input type="radio" name="visible" value="0"
                                   @checked(old('visible',1)==0)
                                   class="rounded border-slate-400 text-slate-700 focus:ring-slate-500">
                            <span>非表示</span>
                        </label>
                    </div>
                    @error('visible') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end">
                    <button class="rounded-md bg-slate-900 text-white px-4 py-2 text-sm hover:bg-slate-800">
                        追加
                    </button>
                </div>
            </div>
        </form>

        {{-- 一覧（行内：変更ボタンでAjax更新／削除ボタンでAjax削除） --}}
        <div class="rounded-md border border-slate-200 bg-white p-4 shadow-sm">
            <table class="w-full text-sm">
                <thead>
                <tr class="text-left text-slate-500">
                    <th class="py-2 pr-3">ID</th>
                    <th class="py-2 pr-3">シナリオ要素</th>
                    <th class="py-2 pr-3">表示</th>
                    <th class="py-2 pr-3 w-36">操作</th>
                </tr>
                </thead>
                <tbody id="kindTable">
                @foreach ($elements as $k)
                    <tr class="border-t border-slate-200"
                        data-id="{{ $k->id }}"
                        data-update-url="{{ route('elements.update', $k) }}"
                        data-destroy-url="{{ route('elements.destroy', $k) }}"
                        data-original-title="{{ $k->title }}"
                        data-original-visible="{{ $k->visible ? 1 : 0 }}">
                        <td class="py-2 pr-3 text-slate-500">{{ $k->id }}</td>

                        <td class="py-2 pr-3">
                            <input type="text" value="{{ $k->title }}" maxlength="100"
                                   class="block w-96 rounded-md border border-slate-300 px-2 py-1
                                      focus:outline-none focus:ring-2 focus:ring-slate-500">
                            <p class="mt-1 text-xs text-red-600 hidden js-error-title"></p>
                        </td>

                        <td class="py-2 pr-3">
                            <label class="inline-flex items-center gap-2 m-lg-3">
                                <input type="checkbox" {{ $k->visible ? 'checked' : '' }}>
                                <span class="text-slate-600">表示</span>
                            </label>
                            <p class="mt-1 text-xs text-red-600 hidden js-error-visible"></p>
                        </td>

                        <td class="py-2 pr-3">
                            <div class="flex items-center gap-2">
                                <button type="button" class="px-3 py-1 rounded border border-slate-300 js-save">変更</button>
                                <button type="button" class="px-3 py-1 rounded border border-slate-300 js-delete">削除</button>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Ajax（vanilla fetch） --}}
    <script>
        (function(){
            const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const table = document.getElementById('kindTable');

            const flashRow = (tr, ok=true) => {
                tr.classList.remove('ring-2','ring-emerald-400','ring-rose-400');
                tr.classList.add('ring-2', ok ? 'ring-emerald-400' : 'ring-rose-400');
                setTimeout(()=> tr.classList.remove('ring-2','ring-emerald-400','ring-rose-400'), 700);
            };

            const clearErrors = (tr) => {
                ['.js-error-title','.js-error-visible'].forEach(sel => {
                    const el = tr.querySelector(sel);
                    if (el) { el.textContent = ''; el.classList.add('hidden'); }
                });
            };

            const showErrors = (tr, errors) => {
                if (errors?.title)   { const el=tr.querySelector('.js-error-title');   el.textContent = errors.title.join(' ');   el.classList.remove('hidden'); }
                if (errors?.visible) { const el=tr.querySelector('.js-error-visible'); el.textContent = errors.visible.join(' '); el.classList.remove('hidden'); }
            };

            const getState = (tr) => ({
                title: tr.querySelector('input[type="text"]').value.trim(),
                visible: tr.querySelector('input[type="checkbox"]').checked ? 1 : 0,
            });

            const isDirty = (tr) => {
                const s = getState(tr);
                const origTitle   = tr.dataset.originalTitle ?? '';
                const origVisible = Number(tr.dataset.originalVisible ?? 0);
                return s.title !== origTitle || s.visible !== origVisible;
            };

            const updateSaveButton = (tr) => {
                const btn = tr.querySelector('.js-save');
                if (btn) btn.disabled = !isDirty(tr);
            };

            // 入力変更 → ボタンの有効/無効だけ切替（即保存はしない）
            table?.addEventListener('input', (e) => {
                const tr = e.target.closest('tr[data-update-url]');
                if (tr) updateSaveButton(tr);
            });
            table?.addEventListener('change', (e) => {
                const tr = e.target.closest('tr[data-update-url]');
                if (tr) updateSaveButton(tr);
            });

            // 保存（PATCH）
            const save = async (tr) => {
                clearErrors(tr);
                const { title, visible } = getState(tr);
                const url = tr.dataset.updateUrl;

                const res = await fetch(url, {
                    method: 'PATCH',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ title, visible }),
                });

                if (res.ok) {
                    tr.dataset.originalTitle = title;
                    tr.dataset.originalVisible = String(visible);
                    updateSaveButton(tr);
                    flashRow(tr, true);
                    return;
                }
                if (res.status === 422) {
                    const data = await res.json();
                    showErrors(tr, data.errors || {});
                    flashRow(tr, false);
                } else {
                    alert('更新に失敗しました（' + res.status + '）');
                    flashRow(tr, false);
                }
            };

            // 削除（DELETE）
            const destroyRow = async (tr) => {
                if (!confirm('このシナリオ要素を削除しますか？')) return;
                const url = tr.dataset.destroyUrl;

                const res = await fetch(url, {
                    method: 'DELETE',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                });

                if (res.ok) tr.remove();
                else alert('削除に失敗しました（' + res.status + '）');
            };

            // クリック（変更／削除）
            table?.addEventListener('click', (e) => {
                const tr = e.target.closest('tr[data-update-url]');
                if (!tr) return;
                if (e.target.closest('.js-save'))   { e.preventDefault(); save(tr); }
                if (e.target.closest('.js-delete')) { e.preventDefault(); destroyRow(tr); }
            });

            // 初期状態
            document.querySelectorAll('#kindTable tr[data-update-url]').forEach(updateSaveButton);
        })();
    </script>

    <script>
        (function () {
            // 1) 対象テーブルを取得（必要なら #kindTable の親table に限定）
            const tbody = document.querySelector('#kindTable');
            if (!tbody) return;
            const table = tbody.closest('table');
            const ths = table.querySelectorAll('thead th');

            // 2) ヘッダから「ID」「シナリオ要素」の列インデックスを自動特定
            let idxId = -1, idxTitle = -1;
            ths.forEach((th, i) => {
                const label = th.textContent.replace(/\s/g, '');
                if (label.includes('ID')) idxId = i;
                if (label.includes('シナリオ要素')) idxTitle = i;
            });
            if (idxId < 0 && idxTitle < 0) return;

            // 3) クリック可能化 + アイコン設置
            const state = { key: null, dir: 1 }; // dir: 1=ASC, -1=DESC
            const makeClickable = (th, key) => {
                if (!th) return;
                th.style.cursor = 'pointer';
                th.classList.add('select-none');
                const icon = document.createElement('span');
                icon.className = 'js-sort-icon';
                icon.style.marginLeft = '0.25rem';
                icon.textContent = '↕';
                th.appendChild(icon);
                th.addEventListener('click', () => sortBy(key, th));
            };

            makeClickable(ths[idxId], 'id');
            makeClickable(ths[idxTitle], 'title');

            // 4) 値の取り方（IDはテキスト、要素は input の値を優先）
            const getter = (tr, key) => {
                const cells = tr.children;
                if (key === 'id') {
                    const cell = cells[idxId];
                    const n = parseInt((cell?.textContent || '').trim(), 10);
                    return isNaN(n) ? 0 : n;
                }
                if (key === 'title') {
                    const cell = cells[idxTitle];
                    const inp = cell?.querySelector('input[type="text"]');
                    const val = (inp ? inp.value : cell?.textContent || '').trim().toLowerCase();
                    return val;
                }
                return '';
            };

            // 5) ソート本体
            const sortBy = (key, th) => {
                // 同じ列を再クリック → 方向反転、違う列なら昇順から
                state.dir = (state.key === key) ? -state.dir : 1;
                state.key = key;

                const rows = Array.from(tbody.querySelectorAll('tr[data-id]'));
                rows.sort((a, b) => {
                    const va = getter(a, key), vb = getter(b, key);
                    if (va < vb) return -1 * state.dir;
                    if (va > vb) return  1 * state.dir;
                    return 0;
                });
                rows.forEach(r => tbody.appendChild(r)); // 再配置（イベント委譲は維持）

                // ヘッダのアイコン更新
                ths.forEach(h => {
                    const i = h.querySelector('.js-sort-icon');
                    if (!i) return;
                    i.textContent = '↕';
                    h.classList.remove('font-semibold');
                });
                const icon = th.querySelector('.js-sort-icon');
                if (icon) icon.textContent = (state.dir === 1 ? '↑' : '↓');
                th.classList.add('font-semibold');
            };
        })();
    </script>

@endsection
