{{-- シナリオ一覧（バニラJSで検索・絞り込み・並べ替え） --}}
@extends('layouts.admin')

@section('title', 'シナリオ管理')
@section('page_title', 'シナリオ一覧')
@section('page_desc', '絞り込み検索/ポップアップは消えない場合はESCボタンで消えます')

@section('content')
    @php
        use Illuminate\Support\Facades\Storage;
        use Illuminate\Support\Str;
    @endphp

        <!-- フィルタ -->
    <div class="rounded-md border border-slate-200 bg-white p-4 shadow-sm space-y-4">
        <!-- 本文検索（タイトル/本文/URL を部分一致） -->
        <div>
            <label for="q" class="block text-sm font-medium text-slate-700">本文検索</label>
            <input id="q" type="text" class="form-input mt-1" placeholder="語句で検索（タイトル/本文/URL）">
        </div>

        <!-- シナリオ種別（横並びチェック） -->
        <div>
            <div class="flex items-center justify-between">
                <label class="block text-sm font-medium text-slate-700">シナリオ種別（複数選択）</label>
                <div class="flex gap-2">
                    <button type="button" class="rounded-md border px-2 py-1 text-xs" id="selectAllKinds">全選択</button>
                    <button type="button" class="rounded-md border px-2 py-1 text-xs" id="clearKinds">クリア</button>
                </div>
            </div>
            <div id="kindFilters" class="mt-2 flex flex-wrap gap-3">
                @forelse($kinds as $kind)
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="checkbox"
                               class="js-kind rounded-md border border-slate-400 focus:border-slate-600 focus:ring-slate-500"
                               value="{{ $kind->id }}">
                        <span class="select-none">{{ $kind->title }}</span>
                    </label>
                @empty
                    <p class="text-sm text-slate-500">シナリオ種別はまだありません。</p>
                @endforelse
            </div>
        </div>

        <!-- シナリオ要素（横並びチェック） -->
        <div>
            <div class="flex items-center justify-between">
                <label class="block text-sm font-medium text-slate-700">シナリオ要素（複数選択）</label>
                <div class="flex gap-2">
                    <button type="button" class="rounded-md border px-2 py-1 text-xs" id="selectAllElements">全選択</button>
                    <button type="button" class="rounded-md border px-2 py-1 text-xs" id="clearElements">クリア</button>
                </div>
            </div>
            <div id="elementFilters" class="mt-2 flex flex-wrap gap-3">
                @forelse($elements as $el)
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="checkbox"
                               class="js-element rounded-md border border-slate-400 focus:border-slate-600 focus:ring-slate-500"
                               value="{{ $el->id }}">
                        <span class="select-none">{{ $el->title }}</span>
                    </label>
                @empty
                    <p class="text-sm text-slate-500">シナリオ要素はまだありません。</p>
                @endforelse
            </div>
        </div>
        <p id="resultCount" class="text-xs text-slate-600">表示件数: 0 件</p>
    </div>

    <!-- 一覧テーブル -->
    <div class="rounded-md border border-slate-200 bg-white shadow-sm overflow-x-auto mt-4">
        <table class="table-auto w-full" id="scenarioTable">
            <thead class="text-left text-slate-600 border-b">
            <tr>
                <th class="py-2 px-3">
                    <button type="button" class="inline-flex items-center gap-1 js-sort" data-key="id">
                        ID <span class="js-sort-icon text-slate-400">↕</span>
                    </button>
                </th>
                <th class="py-2 px-3" style="white-space:nowrap;">メイン画像</th>
                <th class="py-2 px-3">
                    <button type="button" class="inline-flex items-center gap-1 js-sort" data-key="title">
                        タイトル <span class="js-sort-icon text-slate-400">↕</span>
                    </button>
                </th>
                <th class="py-2 px-3">URL</th>
                <th class="py-2 px-3">
                    <button type="button" class="inline-flex items-center gap-1 js-sort" data-key="body">
                        本文 <span class="js-sort-icon text-slate-400">↕</span>
                    </button>
                </th>
                <th class="py-2 px-3">
                    <button type="button" class="inline-flex items-center gap-1 js-sort" data-key="created_at">
                        登録日 <span class="js-sort-icon text-slate-400">↕</span>
                    </button>
                </th>
                <th class="py-2 px-3">
                    <button type="button" class="inline-flex items-center gap-1 js-sort" data-key="tags">
                        シナリオ種別 <span class="js-sort-icon text-slate-400">↕</span>
                    </button>
                </th>
                <th class="py-2 px-3">
                    <button type="button" class="inline-flex items-center gap-1 js-sort" data-key="elements">
                        シナリオ要素 <span class="js-sort-icon text-slate-400">↕</span>
                    </button>
                </th>
            </tr>
            </thead>
            <tbody id="scenarioTbody">
            @forelse($scenarios as $scenario)
                @php
                    $img       = $scenario->image ? Storage::url($scenario->image) : asset('images/no_image.png');
                    $tagIds    = $scenario->kinds->pluck('id')->implode(',');
                    $tagText   = $scenario->kinds->pluck('title')->implode(', ');

                    // 追加：elements 用
                    $elIds     = $scenario->elements->pluck('id')->implode(',');
                    $elText    = $scenario->elements->pluck('title')->implode(', ');

                    $ts        = optional($scenario->created_at)->timestamp ?? 0; // 並べ替え用
                @endphp
                <tr class="border-b last:border-0 hover:bg-slate-50 align-top"
                    data-id="{{ $scenario->id }}"
                    data-title="{{ e($scenario->title) }}"
                    data-body="{{ e($scenario->body) }}"
                    data-url="{{ e($scenario->url) }}"
                    data-created-ts="{{ $ts }}"
                    data-tags="{{ $tagIds }}"
                    data-tag-names="{{ e($tagText) }}"
                    data-elements="{{ $elIds }}"
                    data-element-names="{{ e($elText) }}"
                >
                    <td class="py-2 px-3 font-medium text-slate-900">
                        <a href="{{ route('scenarios.edit', $scenario) }}">{{ $scenario->id }}</a>
                    </td>
                    <td class="py-2 px-3">
                        <a href="{{ route('scenarios.show', $scenario) }}">
                            <img src="{{ $img }}" alt="" class="w-16 h-16 object-cover rounded-md ring-1 ring-slate-200 bg-slate-50">
                        </a>
                    </td>
                    <td class="py-2 px-3">
                        <div class="font-medium text-slate-900">
                            <a href="{{ route('scenarios.show', $scenario) }}">{{ $scenario->title }}</a>
                        </div>
                    </td>
                    <td class="py-2 px-3">
                        <div class="font-medium text-slate-900">
                            <a href="{{ $scenario->url }}" target="_blank"><i class="fa-solid fa-link"></i></a>
                        </div>
                    </td>
                    <td class="py-2 px-3 text-slate-700 js-body">
                        {{ Str::limit($scenario->body, 120) }}
                    </td>
                    <td class="py-2 px-3 text-slate-500">
                        {{ optional($scenario->created_at)->format('Y-m-d H:i') }}
                    </td>
                    <td class="py-2 px-3">
                        <div class="flex flex-wrap gap-1">
                            @forelse($scenario->kinds as $k)
                                <span class="px-2 py-0.5 rounded-md text-xs bg-slate-50 ring-1 ring-slate-200 text-slate-700">
                                    {{ $k->title }}
                                </span>
                            @empty
                                <span class="text-xs text-slate-400">（なし）</span>
                            @endforelse
                        </div>
                    </td>
                    <td class="py-2 px-3">
                        <div class="flex flex-wrap gap-1">
                            @forelse($scenario->elements as $el)
                                <span class="px-2 py-0.5 rounded-md text-xs bg-slate-50 ring-1 ring-slate-200 text-slate-700">
                                    {{ $el->title }}
                                </span>
                            @empty
                                <span class="text-xs text-slate-400">（なし）</span>
                            @endforelse
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="py-6 px-3 text-center text-slate-500">シナリオはありません。</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Vanilla JS：検索・絞り込み・並べ替え --}}
    <script>
        (function(){
            const qInput       = document.getElementById('q');
            const kindCtn      = document.getElementById('kindFilters');
            const kindBoxes    = () => Array.from(document.querySelectorAll('.js-kind'));
            const selectAll    = document.getElementById('selectAllKinds');
            const clearKinds   = document.getElementById('clearKinds');

            const elementCtn   = document.getElementById('elementFilters');
            const elementBoxes = () => Array.from(document.querySelectorAll('.js-element'));
            const selectAllEl  = document.getElementById('selectAllElements');
            const clearEls     = document.getElementById('clearElements');

            const tbody   = document.getElementById('scenarioTbody');
            const table   = document.getElementById('scenarioTable');
            const rowsAll = Array.from(tbody.querySelectorAll('tr[data-id]'));

            const countEl = document.getElementById('resultCount');
            const sortBtns = Array.from(table.querySelectorAll('.js-sort'));

            const state = {
                q: '',
                kinds: new Set(),     // 選択中の kind_id（文字列）
                elements: new Set(),  // 選択中の element_id（文字列）
                sortKey: 'created_at',
                sortAsc: false,
            };

            const textIncludes = (hay, needle) =>
                hay.toLowerCase().indexOf(needle.toLowerCase()) !== -1;

            const getRowData = (tr) => {
                // データ属性から都度読む（編集しないのでキャッシュ不要）
                const id    = parseInt(tr.dataset.id, 10) || 0;
                const title = tr.dataset.title || '';
                const body  = tr.dataset.body  || '';
                const url   = tr.dataset.url   || '';
                const createdTs = parseInt(tr.dataset.createdTs, 10) || 0;

                const tags      = (tr.dataset.tags || '').split(',').filter(Boolean);      // kind ids
                const tagNames  = (tr.dataset.tagNames || '').toLowerCase();

                const elements     = (tr.dataset.elements || '').split(',').filter(Boolean); // element ids
                const elementNames = (tr.dataset.elementNames || '').toLowerCase();

                return {el: tr, id, title, body, url, createdTs, tags, tagNames, elements, elementNames};
            };

            const applyFiltersAndSort = () => {
                const q = state.q.trim().toLowerCase();
                const filtered = [];

                // 1) フィルタ
                rowsAll.forEach(tr => {
                    const d = getRowData(tr);

                    // 本文検索（title/body/url）
                    const qOK = q === '' ? true :
                        (textIncludes(d.title, q) || textIncludes(d.body, q) || textIncludes(d.url, q));

                    // 種別（選択されたすべてを含む AND。未選択なら全通過）
                    const kindsOK = state.kinds.size === 0
                        ? true
                        : [...state.kinds].every(k => d.tags.includes(String(k)));

                    // 要素（選択されたすべてを含む AND。未選択なら全通過）
                    const elementsOK = state.elements.size === 0
                        ? true
                        : [...state.elements].every(eid => d.elements.includes(String(eid)));

                    const show = qOK && kindsOK && elementsOK;
                    tr.style.display = show ? '' : 'none';
                    if (show) filtered.push(d);
                });

                // 2) 並べ替え（表示対象だけ）
                const key = state.sortKey;
                const dir = state.sortAsc ? 1 : -1;

                filtered.sort((a,b) => {
                    if (key === 'id')         return dir * (a.id - b.id);
                    if (key === 'created_at') return dir * (a.createdTs - b.createdTs);
                    if (key === 'title')      return dir * String(a.title).localeCompare(String(b.title), 'ja');
                    if (key === 'body')       return dir * String(a.body).localeCompare(String(b.body), 'ja');
                    if (key === 'tags')       return dir * String(a.tagNames).localeCompare(String(b.tagNames), 'ja');
                    if (key === 'elements')   return dir * String(a.elementNames).localeCompare(String(b.elementNames), 'ja');
                    return 0;
                });

                // 3) DOM 再配置（対象のみ順序を揃える）
                filtered.forEach(d => tbody.appendChild(d.el));

                // 4) カウント
                if (countEl) countEl.textContent = `表示件数: ${filtered.length} 件 / 全 ${rowsAll.length} 件`;
            };

            const debounce = (fn, ms=200) => {
                let t; return (...args) => { clearTimeout(t); t = setTimeout(()=>fn(...args), ms); };
            };

            // --- イベント
            qInput?.addEventListener('input', debounce((e) => {
                state.q = e.target.value || '';
                applyFiltersAndSort();
            }, 200));

            kindCtn?.addEventListener('change', (e) => {
                const cb = e.target.closest('.js-kind');
                if (!cb) return;
                if (cb.checked) state.kinds.add(cb.value);
                else state.kinds.delete(cb.value);
                applyFiltersAndSort();
            });

            selectAll?.addEventListener('click', () => {
                kindBoxes().forEach(cb => { cb.checked = true; state.kinds.add(cb.value); });
                applyFiltersAndSort();
            });

            clearKinds?.addEventListener('click', () => {
                kindBoxes().forEach(cb => { cb.checked = false; });
                state.kinds.clear();
                applyFiltersAndSort();
            });

            elementCtn?.addEventListener('change', (e) => {
                const cb = e.target.closest('.js-element');
                if (!cb) return;
                if (cb.checked) state.elements.add(cb.value);
                else state.elements.delete(cb.value);
                applyFiltersAndSort();
            });

            selectAllEl?.addEventListener('click', () => {
                elementBoxes().forEach(cb => { cb.checked = true; state.elements.add(cb.value); });
                applyFiltersAndSort();
            });

            clearEls?.addEventListener('click', () => {
                elementBoxes().forEach(cb => { cb.checked = false; });
                state.elements.clear();
                applyFiltersAndSort();
            });

            sortBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    const key = btn.dataset.key;
                    if (state.sortKey === key) {
                        state.sortAsc = !state.sortAsc;
                    } else {
                        state.sortKey = key;
                        state.sortAsc = true;
                    }
                    // アイコン更新
                    sortBtns.forEach(b => b.querySelector('.js-sort-icon').textContent = '↕');
                    btn.querySelector('.js-sort-icon').textContent = state.sortAsc ? '↑' : '↓';
                    applyFiltersAndSort();
                });
            });

            // 初期：登録日降順（created_at desc）
            state.sortKey = 'created_at';
            state.sortAsc = false;
            // アイコン初期化
            sortBtns.forEach(b => {
                const ic = b.querySelector('.js-sort-icon');
                ic.textContent = (b.dataset.key === 'created_at') ? '↓' : '↕';
            });

            // 初回適用
            applyFiltersAndSort();
        })();
    </script>

    {{-- Vanilla JS：本文ホバーツールチップ（セル左上固定） --}}
    <script>
        (function(){
            const tbody = document.getElementById('scenarioTbody');
            if (!tbody) return;

            const tip = document.createElement('div');
            Object.assign(tip.style, {
                position: 'fixed',
                zIndex: '50',
                display: 'none',
                pointerEvents: 'auto',
                maxWidth: 'min(600px, 80vw)',
                maxHeight: '70vh',
                overflow: 'auto',
                whiteSpace: 'pre-wrap',
                wordBreak: 'break-word',
                background: 'white',
                border: '1px solid rgba(100,116,139,0.3)',
                borderRadius: '0.5rem',
                padding: '0.75rem',
                boxShadow: '0 8px 24px rgba(0,0,0,0.12)',
                fontSize: '0.875rem',
                color: '#0f172a',
                cursor: 'default'
            });
            document.body.appendChild(tip);

            let currentCell = null;
            let overTip = false;

            const showTipAtCell = (cell, text) => {
                tip.textContent = text || '';
                if (!text) { hideTip(); return; }
                tip.style.display = 'block';
                positionTipAtCell(cell);
            };

            const hideTip = () => {
                tip.style.display = 'none';
                tip.textContent = '';
            };

            const positionTipAtCell = (cell) => {
                if (!cell) return;
                const c = cell.getBoundingClientRect();
                tip.style.left = '0px';
                tip.style.top  = '0px';
                const rect = tip.getBoundingClientRect();

                const vw = window.innerWidth;
                const vh = window.innerHeight;
                const margin = 6;

                let left = c.left + margin;
                let top  = c.top  + margin;

                if (left + rect.width > vw - 8) left = Math.max(8, vw - rect.width - 8);
                if (top  + rect.height > vh - 8) top  = Math.max(8, vh - rect.height - 8);
                left = Math.max(8, left);
                top  = Math.max(8, top);

                tip.style.left = `${left}px`;
                tip.style.top  = `${top}px`;
            };

            const onMouseOver = (e) => {
                const cell = e.target.closest('td.js-body');
                if (!cell || !tbody.contains(cell)) return;
                currentCell = cell;
                const tr = cell.closest('tr[data-id]');
                const full = tr?.dataset?.body || '';
                showTipAtCell(currentCell, full);
            };

            const onMouseOut = (e) => {
                const leftCell = e.target.closest?.('td.js-body');
                const intoTip  = e.relatedTarget && (e.relatedTarget === tip || tip.contains(e.relatedTarget));
                if (leftCell && !intoTip) {
                    currentCell = null;
                    if (!overTip) hideTip();
                }
            };

            tip.addEventListener('mouseenter', () => { overTip = true; });
            tip.addEventListener('mouseleave', () => {
                overTip = false;
                if (!currentCell) hideTip();
            });

            const onScrollOrResize = () => {
                if (tip.style.display === 'none') return;
                if (currentCell) {
                    positionTipAtCell(currentCell);
                } else if (!overTip) {
                    hideTip();
                }
            };

            tbody.addEventListener('mouseover', onMouseOver);
            tbody.addEventListener('mouseout',  onMouseOut);
            window.addEventListener('scroll', onScrollOrResize, { passive: true });
            window.addEventListener('resize', onScrollOrResize);

            // ★ ESCキーで閉じる処理を追加
            window.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    currentCell = null;
                    overTip = false;
                    hideTip();
                }
            });
        })();
    </script>


@endsection
