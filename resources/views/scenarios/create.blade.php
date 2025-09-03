@extends('layouts.admin')

@section('title', 'シナリオ登録')
@section('page_title', 'シナリオ登録')
@section('page_desc', 'シナリオ名、URL、本文、表示/非表示、タグA/タグB（その場で追加・削除・複数選択）、メモ、メイン画像ファイルを登録します。')

@section('content')
    <form action="{{ route('scenarios.store') }}" method="post" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- 基本情報 -->
        <div class="rounded-md border border-slate-200 bg-white p-4 shadow-sm space-y-4">
            @if ($errors->any())
                <div class="mb-8 py-4 px-6 border border-red-300 bg-red-50 rounded">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li class="text-red-400">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- シナリオ名 --}}
            <div>
                <label class="block text-sm font-medium text-slate-700">シナリオ名</label>
                <input id="title" name="title" type="text" class="form-input mt-1"
                       placeholder="シナリオタイトル" value="{{ old('title') }}" maxlength="100">
                <p class="mt-1 text-xs text-slate-500">
                    <span id="titleCount">{{ mb_strlen(old('title', '')) }}</span>/100
                </p>
            </div>

            {{-- URL --}}
            <div>
                <label class="block text-sm font-medium text-slate-700">URL <span class="text-rose-600">*</span></label>
                <input id="url" name="url" type="url" pattern="https?://.+"
                       class="form-input mt-1" placeholder="https://example.com/scenario"
                       value="{{ old('url') }}">
                <p class="mt-1 text-xs" id="urlMsg" aria-live="polite">
                </p>
            </div>

            {{-- 本文 --}}
            <div>
                <label class="block text-sm font-medium text-slate-700">シナリオ本文</label>
                <textarea id="body" name="body" rows="8" class="form-input mt-1" placeholder="シナリオの概略を書いてください。" maxlength="2000">{{ old('body') }}</textarea>
                <div class="mt-1 flex items-center justify-between text-xs">
                    <span class="text-slate-500"><span id="bodyCount">{{ mb_strlen(old('body','')) }}</span>/2000</span>
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="keep_line_breaks"
                               class="rounded-md border border-slate-400 focus:border-slate-600 focus:ring-slate-500"
                            @checked(old('keep_line_breaks', true))>
                        <span class="text-slate-600">改行を保持</span>
                    </label>
                </div>
            </div>

            {{-- 表示設定 --}}
            <div>
                <span class="block text-sm font-medium text-slate-700">表示設定</span>
                <div class="mt-2 flex items-center gap-4">
                    <label class="inline-flex items-center gap-2">
                        <input type="radio" name="visible" value="1"
                               class="rounded-md border border-slate-400 focus:border-slate-600 focus:ring-slate-500"
                            @checked(old('visible', 1) == 1)>
                        <span>表示</span>
                    </label>
                    <label class="inline-flex items-center gap-2">
                        <input type="radio" name="visible" value="0"
                               class="rounded-md border border-slate-400 focus:border-slate-600 focus:ring-slate-500"
                            @checked(old('visible', 1) == 0)>
                        <span>非表示</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="rounded-md border border-slate-200 bg-white p-4 shadow-sm space-y-6">
            <div>
                <div class="flex items-center justify-between">
                    <label class="block text-sm font-medium text-slate-700">シナリオ種別（複数選択）</label>
                    <div class="flex gap-2">
                        <button type="button" class="rounded-md border border-slate-200 px-2 py-1 text-xs"
                                data-select-all="kinds">全選択</button>
                        <button type="button" class="rounded-md border border-slate-200 px-2 py-1 text-xs"
                                data-clear="kinds">選択解除</button>
                    </div>
                </div>

                {{-- ここが “その場で新規登録” 入力UI --}}
                <div class="mt-2 flex gap-2">
                    <input id="newKindTitle" type="text" class="form-input flex-1" placeholder="新しいシナリオ種別を入力して Enter / 追加">
                    <button type="button" id="addKindBtn" class="rounded-md border border-slate-200 px-3 py-2 text-sm">追加</button>
                </div>
                <p id="newKindError" class="mt-1 text-xs text-red-600" style="display:none"></p>
                <p id="newKindOk" class="mt-1 text-xs text-emerald-600" style="display:none"></p>

                @php
                    $oldKindIds = collect(old('kinds', []))->map(fn($v)=>(string)$v)->all();
                @endphp

                <div id="kindsContainer" class="mt-3 flex flex-wrap gap-3">
                    @forelse ($kinds as $kind)
                        @php $checked = in_array((string)$kind->id, $oldKindIds, true); @endphp
                        <div class="flex items-center gap-2 px-2 py-1 rounded-md ring-1 ring-slate-200 bg-slate-50" data-kind-id="{{ $kind->id }}">
                            <label class="inline-flex items-center gap-2 text-sm">
                                <input type="checkbox" name="kinds[]"
                                       class="rounded-md border border-slate-400 focus:border-slate-600 focus:ring-slate-500"
                                       value="{{ $kind->id }}" @checked($checked)>
                                <span class="select-none">{{ $kind->title }}</span>
                            </label>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">選択できるシナリオ種別がありません。</p>
                    @endforelse
                </div>

                <p class="mt-2 text-xs text-slate-500">選択中：<span id="selectedKindsList">（読み込み中）</span></p>
                @error('kinds')    <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                @error('kinds.*')  <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- メモ -->
        <div class="rounded-md border border-slate-200 bg-white p-4 shadow-sm">
            <label class="block text-sm font-medium text-slate-700">メモ</label>
            <textarea id="memo" name="memo" rows="4" maxlength="1000"
                      class="form-input mt-1" placeholder="補足事項や運用ルールなどを記入">{{ old('memo') }}</textarea>
            <p class="mt-1 text-xs text-slate-500"><span id="memoCount">{{ mb_strlen(old('memo','')) }}</span>/1000</p>
        </div>

        <!-- メイン画像 -->
        <div class="rounded-md border border-slate-200 bg-white p-4 shadow-sm">
            <label class="block text-sm font-medium text-slate-700">メイン画像ファイル</label>
            <div class="mt-1 flex items-start gap-4">
                <input id="imageInput" name="image" type="file" accept="image/*" class="form-input">

                <div class="flex items-start gap-3" id="previewWrap" style="display:none">
                    <img id="previewImg" alt="preview"
                         class="w-24 h-24 object-cover rounded-md ring-1 ring-slate-200 bg-slate-50">
                    <div class="text-xs leading-6" id="previewMeta"></div>
                    <button type="button"
                            class="rounded-md border border-slate-200 px-3 py-2 text-sm h-9"
                            id="clearImageBtn">削除</button>
                </div>
            </div>
            <p class="mt-1 text-xs text-slate-500">推奨：JPG/PNG、5MB 以下。</p>
            <p id="imageError" class="mt-1 text-xs text-red-600" style="display:none"></p>
        </div>

        <!-- アクション -->
        <div class="flex items-center justify-end gap-2">
            <a href="#" class="rounded-md border border-slate-200 bg-white px-4 py-2 text-sm hover:bg-slate-50">キャンセル</a>
            <button type="submit" class="rounded-md bg-slate-900 text-white px-4 py-2 text-sm hover:bg-slate-800">
                登録する
            </button>
        </div>
    </form>

    {{-- Vanilla JS --}}
    <script>
        (function(){
            // ---（前半：title/body/memoの文字数・URLメッセ 等は既存のまま）---

            // --- シナリオ種別（kinds）: 表示更新・全選択/解除 ---
            const kindsCtn   = document.getElementById('kindsContainer');
            const selectedEl = document.getElementById('selectedKindsList');
            const updateSelectedKindsText = () => {
                if (!kindsCtn || !selectedEl) return;
                const selected = [...kindsCtn.querySelectorAll('input[type=checkbox]:checked')]
                    .map(cb => cb.closest('label')?.querySelector('span')?.textContent?.trim())
                    .filter(Boolean);
                selectedEl.textContent = selected.length ? selected.join(', ') : 'なし';
            };
            kindsCtn?.addEventListener('change', e => {
                if (e.target.matches('input[type=checkbox]')) updateSelectedKindsText();
            });
            document.querySelector('[data-select-all="kinds"]')?.addEventListener('click', () => {
                kindsCtn?.querySelectorAll('input[type=checkbox]').forEach(cb => cb.checked = true);
                updateSelectedKindsText();
            });
            document.querySelector('[data-clear="kinds"]')?.addEventListener('click', () => {
                kindsCtn?.querySelectorAll('input[type=checkbox]').forEach(cb => cb.checked = false);
                updateSelectedKindsText();
            });

            // --- “その場で新規登録”（Ajax） ---
            const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const newInput = document.getElementById('newKindTitle');
            const addBtn   = document.getElementById('addKindBtn');
            const errEl    = document.getElementById('newKindError');
            const okEl     = document.getElementById('newKindOk');

            const normalize = (s) => (s || '').trim().replace(/\s+/g, ' ');

            const existsInListByTitle = (title) => {
                const titles = [...kindsCtn.querySelectorAll('label span')].map(s => s.textContent.trim().toLowerCase());
                return titles.includes(title.trim().toLowerCase());
            };

            const appendKindChip = ({id, title}) => {
                // 既に存在していれば、そのチェックをONにして終わり
                const exists = kindsCtn.querySelector(`[data-kind-id="${id}"]`);
                if (exists) {
                    exists.querySelector('input[type=checkbox]').checked = true;
                    updateSelectedKindsText();
                    return;
                }
                // 新規DOMを追加（×削除なし）
                const div = document.createElement('div');
                div.className = 'flex items-center gap-2 px-2 py-1 rounded-md ring-1 ring-slate-200 bg-slate-50';
                div.dataset.kindId = String(id);
                div.innerHTML = `
      <label class="inline-flex items-center gap-2 text-sm">
        <input type="checkbox" name="kinds[]" class="rounded-md border border-slate-400 focus:border-slate-600 focus:ring-slate-500" value="${id}" checked>
        <span class="select-none"></span>
      </label>
    `;
                div.querySelector('span').textContent = title;
                kindsCtn.appendChild(div);
                updateSelectedKindsText();
            };

            const showError = (msg) => {
                if (okEl) { okEl.style.display='none'; okEl.textContent=''; }
                if (errEl) { errEl.textContent = msg; errEl.style.display=''; }
            };
            const showOk = (msg) => {
                if (errEl) { errEl.style.display='none'; errEl.textContent=''; }
                if (okEl) { okEl.textContent = msg; okEl.style.display=''; }
            };

            const addKind = async () => {
                const raw = newInput?.value ?? '';
                const title = normalize(raw);
                if (!title) { showError('シナリオ種別を入力してください。'); return; }

                // 画面上に同名があるならAjaxせずチェックONだけ
                if (existsInListByTitle(title)) {
                    // 既存の要素をチェックON
                    [...kindsCtn.querySelectorAll('label')].forEach(l => {
                        if (l.querySelector('span')?.textContent?.trim().toLowerCase() === title.toLowerCase()) {
                            l.querySelector('input[type=checkbox]').checked = true;
                        }
                    });
                    updateSelectedKindsText();
                    showOk('既に一覧にあるため選択しました。');
                    newInput.value = '';
                    return;
                }

                // Ajax で新規作成
                try {
                    addBtn.disabled = true;
                    showOk(''); showError('');
                    const res = await fetch(`{{ route('kinds.inline') }}`, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ title }),
                    });

                    if (res.ok) {
                        const data = await res.json();
                        appendKindChip(data.kind);
                        showOk('シナリオ種別を追加しました。');
                        newInput.value = '';
                    } else if (res.status === 422) {
                        const data = await res.json();
                        const msg = (data?.errors?.title && data.errors.title.join(' ')) || '既に登録されています。';
                        showError(msg);
                    } else {
                        showError('追加に失敗しました（' + res.status + '）。');
                    }
                } catch (e) {
                    showError('通信に失敗しました。ネットワークをご確認ください。');
                } finally {
                    addBtn.disabled = false;
                }
            };

            addBtn?.addEventListener('click', addKind);
            newInput?.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') { e.preventDefault(); addKind(); }
            });

            // 初期表示
            updateSelectedKindsText();
        })();
    </script>

    <script>
        (function(){
            // ...（既存処理）...

            // 画像プレビュー & 簡易バリデーション
            const imageInput  = document.getElementById('imageInput');
            const previewWrap = document.getElementById('previewWrap');
            const previewImg  = document.getElementById('previewImg');
            const previewMeta = document.getElementById('previewMeta');
            const clearBtn    = document.getElementById('clearImageBtn');
            const imageError  = document.getElementById('imageError');

            const KB_MAX = 50000;                 // rules: max:5000 (KB)
            const BYTES_MAX = KB_MAX * 1024;
            const MIN_W = 100, MIN_H = 100;
            const MAX_W = 30000, MAX_H = 30000;

            const fmtBytes = (b) => {
                if (b >= 1024*1024) return (b / (1024*1024)).toFixed(2) + ' MB';
                if (b >= 1024)      return (b / 1024).toFixed(0) + ' KB';
                return b + ' B';
            };

            const showError = (msg) => {
                if (!imageError) return;
                imageError.textContent = msg;
                imageError.style.display = msg ? '' : 'none';
                // ブラウザのネイティブ検証も使う（送信ブロック）
                imageInput.setCustomValidity(msg || '');
            };

            const resetPreview = () => {
                if (previewImg)  previewImg.removeAttribute('src');
                if (previewMeta) previewMeta.textContent = '';
                if (previewWrap) previewWrap.style.display = 'none';
                showError('');
            };

            const buildMetaHtml = (file, w, h) => {
                const esc = (s) => String(s).replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
                return `
      <div><span class="text-slate-500">ファイル名：</span>${esc(file.name)}</div>
      <div><span class="text-slate-500">種類：</span>${esc(file.type || '不明')}</div>
      <div><span class="text-slate-500">サイズ：</span>${fmtBytes(file.size)}（上限 約 ${KB_MAX} KB）</div>
      <div><span class="text-slate-500">解像度：</span>${w} × ${h} px</div>
    `;
            };

            const loadImageDims = (src) => new Promise((resolve, reject) => {
                const img = new Image();
                img.onload = () => resolve({w: img.naturalWidth, h: img.naturalHeight});
                img.onerror = reject;
                img.src = src;
            });

            imageInput?.addEventListener('change', async (e) => {
                const file = e.target.files?.[0];
                if (!file) { resetPreview(); return; }

                // 型チェック
                if (!file.type || !/^image\/(jpeg|png|jpg|webp|gif|bmp|svg\+xml|tiff?)$/i.test(file.type)) {
                    // mimes は jpeg/jpg/png なので、ここでは「画像ではない」だけ伝える
                    showError('画像ファイルを選択してください（JPEG/PNG 推奨）。');
                    resetPreview();
                    return;
                }

                // サイズチェック（サーバ rules: max:5000）
                if (file.size > BYTES_MAX) {
                    showError('ファイルが大きすぎます（上限 約 ' + KB_MAX + ' KB）。');
                    resetPreview();
                    return;
                }

                // プレビュー表示
                const objectUrl = URL.createObjectURL(file);
                previewImg.src = objectUrl;
                previewWrap.style.display = '';
                showError(''); // いったんクリア

                try {
                    const { w, h } = await loadImageDims(objectUrl);

                    // 画面にメタ情報
                    previewMeta.innerHTML = buildMetaHtml(file, w, h);

                    // 解像度チェック（rules: 100~3000）
                    if (w < MIN_W || h < MIN_H || w > MAX_W || h > MAX_H) {
                        showError(`画像の解像度が範囲外です（最小 ${MIN_W}×${MIN_H} / 最大 ${MAX_W}×${MAX_H}）。`);
                    }
                } catch {
                    showError('プレビューの読み込みに失敗しました。別の画像をお試しください。');
                }
            });

            clearBtn?.addEventListener('click', () => {
                imageInput.value = '';
                resetPreview();
            });

            // 初期化（old()で既にファイルは復元できないため、常に非表示から）
            resetPreview();
        })();
    </script>


@endsection
