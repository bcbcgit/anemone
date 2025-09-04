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
@endsection
