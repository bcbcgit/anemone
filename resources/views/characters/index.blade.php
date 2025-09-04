{{-- resources/views/characters/index.blade.php --}}
@extends('layouts.admin')

@section('title','キャラクター管理')
@section('page_title','キャラクター管理')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="rounded-md border border-slate-200 bg-white p-4 shadow-sm">
            <table class="w-full text-sm">
                <thead>
                <tr class="text-left text-slate-500">
                    <th class="py-2 pr-3">ID</th>
                    <th class="py-2 pr-3">キャラクター</th>
                    <th class="py-2 pr-3">ポイント</th>
                    <th class="py-2 pr-3">未使用チケット</th>
                    <th class="py-2 pr-3 w-52">操作</th>
                </tr>
                </thead>
                <tbody id="charTable">
                @foreach($characters as $ch)
                    <tr class="border-t border-slate-200"
                        data-id="{{ $ch->id }}"
                        data-increment-url="{{ route('characters.increment',$ch) }}"
                        data-useone-url="{{ route('tickets.useOne',$ch) }}"
                    >
                        <td class="py-2 pr-3 text-slate-500">{{ $ch->id }}</td>
                        <td class="py-2 pr-3">{{ $ch->name }}</td>
                        <td class="py-2 pr-3">
                            <span class="js-points">{{ number_format($ch->points_total) }}</span>
                        </td>
                        <td class="py-2 pr-3">
                            <span class="js-unused">{{ $ch->tickets_unused_count }}</span>
                        </td>
                        <td class="py-2 pr-3">
                            <div class="flex items-center gap-2">
                                <button type="button" class="px-3 py-1 rounded border border-slate-300 js-plus">+1</button>
                                <button type="button" class="px-3 py-1 rounded border border-slate-300 js-useone">チケットを使う</button>
                            </div>
                            <p class="mt-1 text-xs text-emerald-700 hidden js-flash"></p>
                            <p class="mt-1 text-xs text-rose-700 hidden js-error"></p>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- 既存カードの下に追加 --}}
    <div class="rounded-md border border-slate-200 bg-white p-4 shadow-sm space-y-3">
        <div class="flex items-baseline justify-between">
            <h2 class="text-sm font-semibold text-slate-700">このページ用メモ</h2>
            <div class="text-xs text-slate-500">
                最終保存: <span id="memoUpdatedAt">—</span>
            </div>
        </div>

        <textarea id="memoContent" rows="8"
                  class="block w-full rounded-md border border-slate-300 px-3 py-2 text-sm
               focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-slate-500"
                  placeholder="このページに関するメモを書いて保存できます（テキストファイルに保存されます）"></textarea>

        <div class="flex items-center gap-3">
            <button id="memoSaveBtn"
                    class="px-4 py-2 rounded-md border border-slate-300 bg-slate-50 hover:bg-slate-100 text-sm">
                保存
            </button>
            <span id="memoFlash" class="text-xs text-emerald-700 hidden">保存しました</span>
            <span id="memoError" class="text-xs text-rose-700 hidden"></span>
        </div>
    </div>


    {{-- Ajax（vanilla fetch） --}}
    <script>
        (function(){
            const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const tbody = document.getElementById('charTable');

            const flashRow = (tr, msg, ok=true) => {
                const p = tr.querySelector('.js-flash');
                const e = tr.querySelector('.js-error');
                if (ok) {
                    if (e) { e.classList.add('hidden'); e.textContent=''; }
                    if (p) { p.textContent = msg || '更新しました'; p.classList.remove('hidden'); }
                    tr.classList.remove('ring-2','ring-rose-400');
                    tr.classList.add('ring-2','ring-emerald-400');
                } else {
                    if (p) { p.classList.add('hidden'); p.textContent=''; }
                    if (e) { e.textContent = msg || 'エラーが発生しました'; e.classList.remove('hidden'); }
                    tr.classList.remove('ring-2','ring-emerald-400');
                    tr.classList.add('ring-2','ring-rose-400');
                }
                setTimeout(()=> tr.classList.remove('ring-2','ring-emerald-400','ring-rose-400'), 700);
            };

            const fmt = (n)=> new Intl.NumberFormat().format(n);

            const doPost = async (url) => {
                return fetch(url, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({}),
                });
            };

            const handlePlus = async (tr) => {
                const url = tr.dataset.incrementUrl;
                const res = await doPost(url);
                if (!res.ok) {
                    flashRow(tr, `加算に失敗しました（${res.status}）`, false);
                    return;
                }
                const data = await res.json();
                tr.querySelector('.js-points').textContent = fmt(data.points_total);
                tr.querySelector('.js-unused').textContent = data.tickets_unused;
                flashRow(tr, data.ticket_issued ? '50pt到達—チケット発行！' : '+1しました', true);
            };

            const handleUseOne = async (tr) => {
                const url = tr.dataset.useoneUrl;
                const res = await doPost(url);
                if (!res.ok) {
                    flashRow(tr, `使用に失敗しました（${res.status}）`, false);
                    return;
                }
                const data = await res.json();
                if (data.used) {
                    tr.querySelector('.js-unused').textContent = data.tickets_unused;
                    flashRow(tr, 'チケットを1枚使用しました', true);
                } else {
                    flashRow(tr, data.message || '未使用チケットがありません', false);
                }
            };

            tbody?.addEventListener('click', (e) => {
                const tr = e.target.closest('tr[data-id]');
                if (!tr) return;

                if (e.target.closest('.js-plus'))   { e.preventDefault(); handlePlus(tr); }
                if (e.target.closest('.js-useone')) { e.preventDefault(); handleUseOne(tr); }
            });
        })();
    </script>

    <script>
        (function(){
            const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const elContent = document.getElementById('memoContent');
            const elSave    = document.getElementById('memoSaveBtn');
            const elFlash   = document.getElementById('memoFlash');
            const elError   = document.getElementById('memoError');
            const elUpdated = document.getElementById('memoUpdatedAt');

            const showFlash = (msg='保存しました') => {
                elError.classList.add('hidden'); elError.textContent = '';
                elFlash.textContent = msg; elFlash.classList.remove('hidden');
                setTimeout(()=> elFlash.classList.add('hidden'), 1200);
            };
            const showError = (msg='保存に失敗しました') => {
                elFlash.classList.add('hidden');
                elError.textContent = msg; elError.classList.remove('hidden');
                setTimeout(()=> elError.classList.add('hidden'), 2000);
            };

            // 読み込み
            const loadMemo = async () => {
                try {
                    const res = await fetch(`{{ route('characters.memo.show') }}`, {
                        method: 'GET',
                        credentials: 'same-origin',
                        headers: {'X-Requested-With':'XMLHttpRequest','Accept':'application/json'},
                    });
                    if (!res.ok) throw new Error(res.status);
                    const data = await res.json();
                    elContent.value = data.content || '';
                    elUpdated.textContent = data.updated_at ?? '—';
                } catch (e) {
                    showError('メモの読み込みに失敗しました');
                }
            };

            // 保存
            const saveMemo = async () => {
                try {
                    const res = await fetch(`{{ route('characters.memo.save') }}`, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ content: elContent.value }),
                    });
                    if (!res.ok) {
                        const t = await res.text();
                        throw new Error(t || res.status);
                    }
                    const data = await res.json();
                    elUpdated.textContent = data.updated_at ?? '—';
                    showFlash();
                } catch (e) {
                    showError('保存に失敗しました');
                }
            };

            // クリック保存
            elSave?.addEventListener('click', (e) => {
                e.preventDefault();
                saveMemo();
            });

            // 入力の自動保存（1.5秒停止で保存）
            let timer = null;
            elContent?.addEventListener('input', () => {
                if (timer) clearTimeout(timer);
                timer = setTimeout(saveMemo, 1500);
            });

            // 初回ロード
            loadMemo();
        })();
    </script>

@endsection
