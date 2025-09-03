{{-- 多機能ダッシュボード（テスト・静的） --}}
@extends('layouts.admin')

@section('title', 'ダッシュボード（テスト）')
@section('page_title', 'ダッシュボード')
@section('page_desc', 'KPI・グラフ・リスト・タスク・アクティビティ・通知などの多機能サンプル。')

@section('content')
    <div
        x-data="dashboard()"
        x-init="init()"
        class="space-y-6"
    >
        <!-- 上部コントロール -->
        <div class="flex flex-col md:flex-row md:items-end gap-3">
            <div class="flex-1">
                <label class="block text-sm font-medium text-slate-700">期間</label>
                <div class="mt-1 grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-2">
                    <button @click="range='7d'" :class="btnRange('7d')" class="inline-flex items-center justify-center px-3 py-2 rounded-md border text-sm">7日</button>
                    <button @click="range='30d'" :class="btnRange('30d')" class="inline-flex items-center justify-center px-3 py-2 rounded-md border text-sm">30日</button>
                    <button @click="range='90d'" :class="btnRange('90d')" class="inline-flex items-center justify-center px-3 py-2 rounded-md border text-sm">90日</button>
                    <button @click="range='ytd'" :class="btnRange('ytd')" class="inline-flex items-center justify-center px-3 py-2 rounded-md border text-sm">YTD</button>
                    <div class="col-span-2 md:col-span-2">
                        <div class="flex gap-2">
                            <input type="date" class="form-input" x-model="from">
                            <input type="date" class="form-input" x-model="to">
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full md:w-80">
                <label class="block text-sm font-medium text-slate-700">検索</label>
                <div class="mt-1 relative">
                    <input type="text" class="form-input pl-9" placeholder="注文/顧客/メモで検索…" x-model="q">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.9 14.32a8 8 0 111.414-1.414l3.387 3.387a1 1 0 01-1.414 1.414l-3.387-3.387zM14 8a6 6 0 11-12 0 6 6 0 0112 0z" clip-rule="evenodd"/></svg>
                </div>
            </div>

            <div class="flex gap-2">
                <button class="inline-flex items-center rounded-md border border-slate-200 bg-white px-3 py-2 text-sm hover:bg-slate-50" @click="exportCSV()">エクスポート</button>
                <button class="inline-flex items-center rounded-md bg-slate-900 text-white px-3 py-2 text-sm hover:bg-slate-800" @click="openNew()">新規登録</button>
            </div>
        </div>

        <!-- KPI -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="rounded-md border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate-500">売上（本日）</p>
                        <p class="text-2xl font-semibold text-slate-900" x-text="formatJPY(324000)"></p>
                        <p class="text-xs mt-1" :class="deltaClass(8.2)">前日比 <span x-text="delta(8.2)"></span></p>
                    </div>
                    <!-- sparkline -->
                    <svg class="w-24 h-12" viewBox="0 0 100 50"><polyline fill="none" stroke="currentColor" class="text-emerald-500" stroke-width="3" points="0,40 10,35 20,38 30,30 40,28 50,25 60,20 70,18 80,16 90,12 100,10"/></svg>
                </div>
            </div>

            <div class="rounded-md border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate-500">新規ユーザー</p>
                        <p class="text-2xl font-semibold text-slate-900">124</p>
                        <p class="text-xs mt-1" :class="deltaClass(-2.1)">前週比 <span x-text="delta(-2.1)"></span></p>
                    </div>
                    <svg class="w-24 h-12" viewBox="0 0 100 50"><polyline fill="none" stroke="currentColor" class="text-rose-500" stroke-width="3" points="0,10 10,12 20,13 30,15 40,16 50,20 60,25 70,30 80,34 90,38 100,40"/></svg>
                </div>
            </div>

            <div class="rounded-md border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate-500">注文（本日）</p>
                        <p class="text-2xl font-semibold text-slate-900">86</p>
                        <p class="text-xs mt-1 text-slate-500">平均単価 <span x-text="formatJPY(9800)"></span></p>
                    </div>
                    <svg class="w-24 h-12" viewBox="0 0 100 50"><polyline fill="none" stroke="currentColor" class="text-slate-500" stroke-width="3" points="0,35 10,34 20,33 30,28 40,30 50,26 60,24 70,20 80,22 90,18 100,19"/></svg>
                </div>
            </div>

            <div class="rounded-md border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate-500">在庫アラート</p>
                        <p class="text-2xl font-semibold text-slate-900">5</p>
                        <p class="text-xs mt-1 text-amber-600">要補充: 3 / 要停止: 2</p>
                    </div>
                    <svg class="w-24 h-12" viewBox="0 0 100 50"><rect x="10" y="15" width="12" height="25" class="fill-amber-500"/><rect x="40" y="5" width="12" height="35" class="fill-amber-400"/><rect x="70" y="25" width="12" height="15" class="fill-amber-600"/></svg>
                </div>
            </div>
        </div>

        <!-- グラフ＋指標 -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
            <!-- 1: 売上推移（SVG折れ線） -->
            <div class="xl:col-span-2 rounded-md border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-slate-900">売上推移</h3>
                    <div class="flex gap-2 text-sm">
                        <button class="px-2 py-1 rounded-md border" @click="series='revenue'" :class="tabBtn(series==='revenue')">売上</button>
                        <button class="px-2 py-1 rounded-md border" @click="series='orders'" :class="tabBtn(series==='orders')">注文</button>
                    </div>
                </div>
                <div class="mt-4 overflow-x-auto">
                    <svg viewBox="0 0 800 300" class="w-full min-w-[600px]">
                        <!-- 軸 -->
                        <line x1="40" y1="20" x2="40" y2="260" stroke="#cbd5e1"/>
                        <line x1="40" y1="260" x2="780" y2="260" stroke="#cbd5e1"/>
                        <!-- グリッド -->
                        <g class="text-slate-200" stroke="#e2e8f0">
                            <line x1="40" y1="220" x2="780" y2="220"/><line x1="40" y1="180" x2="780" y2="180"/>
                            <line x1="40" y1="140" x2="780" y2="140"/><line x1="40" y1="100" x2="780" y2="100"/>
                        </g>
                        <!-- ライン -->
                        <polyline
                            :points="chartPoints(series)"
                            fill="none" stroke="#0f172a" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round"
                        />
                        <!-- 点 -->
                        <template x-for="(p,i) in data[series]" :key="i">
                            <circle :cx="40 + i*70" :cy="260 - p/ maxY * 220" r="3" fill="#0f172a"></circle>
                        </template>
                        <!-- ラベル -->
                        <g font-size="10" fill="#64748b">
                            <template x-for="(label,i) in labels" :key="'l'+i">
                                <text :x="40 + i*70" y="275" text-anchor="middle" x-text="label"></text>
                            </template>
                        </g>
                    </svg>
                </div>
            </div>

            <!-- 2: 指標パネル -->
            <div class="rounded-md border border-slate-200 bg-white p-4 shadow-sm space-y-3">
                <h3 class="font-semibold text-slate-900">指標</h3>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div class="rounded-md border border-slate-200 p-3">
                        <p class="text-slate-500">CVR</p>
                        <p class="text-xl font-semibold">2.8%</p>
                    </div>
                    <div class="rounded-md border border-slate-200 p-3">
                        <p class="text-slate-500">AOV</p>
                        <p class="text-xl font-semibold" x-text="formatJPY(10240)"></p>
                    </div>
                    <div class="rounded-md border border-slate-200 p-3">
                        <p class="text-slate-500">直帰率</p>
                        <p class="text-xl font-semibold">34%</p>
                    </div>
                    <div class="rounded-md border border-slate-200 p-3">
                        <p class="text-slate-500">再訪率</p>
                        <p class="text-xl font-semibold">42%</p>
                    </div>
                </div>

                <div class="rounded-md border border-slate-200 p-3">
                    <p class="text-sm text-slate-500 mb-2">チャネル比率</p>
                    <!-- 簡易ドーナツ -->
                    <svg viewBox="0 0 120 120" class="mx-auto w-40 h-40">
                        <circle cx="60" cy="60" r="40" stroke="#e2e8f0" stroke-width="20" fill="none"/>
                        <!-- 3セグメント（例） -->
                        <circle cx="60" cy="60" r="40" stroke="#0ea5e9" stroke-width="20" fill="none"
                                stroke-dasharray="88 251" stroke-dashoffset="0" transform="rotate(-90 60 60)"/>
                        <circle cx="60" cy="60" r="40" stroke="#10b981" stroke-width="20" fill="none"
                                stroke-dasharray="52 287" stroke-dashoffset="-88" transform="rotate(-90 60 60)"/>
                        <circle cx="60" cy="60" r="40" stroke="#f59e0b" stroke-width="20" fill="none"
                                stroke-dasharray="36 303" stroke-dashoffset="-140" transform="rotate(-90 60 60)"/>
                    </svg>
                    <div class="mt-2 grid grid-cols-3 gap-2 text-xs">
                        <div class="flex items-center gap-2"><span class="inline-block w-2 h-2 rounded-full bg-sky-500"></span>広告</div>
                        <div class="flex items-center gap-2"><span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>自然</div>
                        <div class="flex items-center gap-2"><span class="inline-block w-2 h-2 rounded-full bg-amber-500"></span>リファラル</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 注文テーブル ＆ 最近のアクティビティ -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
            <!-- テーブル -->
            <div class="xl:col-span-2 rounded-md border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-slate-900">最近の注文</h3>
                    <div class="flex gap-2">
                        <select class="form-input h-9 w-40" x-model="status">
                            <option value="">すべてのステータス</option>
                            <option value="paid">支払い済み</option>
                            <option value="pending">保留</option>
                            <option value="refunded">返金</option>
                        </select>
                        <select class="form-input h-9 w-32" x-model="perPage">
                            <option value="5">5件</option>
                            <option value="10">10件</option>
                            <option value="20">20件</option>
                        </select>
                    </div>
                </div>
                <div class="mt-3 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-left text-slate-600">
                        <tr class="border-b">
                            <th class="py-2 cursor-pointer" @click="sortBy('id')">注文ID</th>
                            <th class="py-2 cursor-pointer" @click="sortBy('customer')">顧客</th>
                            <th class="py-2 cursor-pointer" @click="sortBy('total')">合計</th>
                            <th class="py-2">ステータス</th>
                            <th class="py-2">日時</th>
                        </tr>
                        </thead>
                        <tbody>
                        <template x-for="o in filteredOrders()" :key="o.id">
                            <tr class="border-b last:border-0 hover:bg-slate-50">
                                <td class="py-2 font-medium text-slate-900" x-text="o.id"></td>
                                <td class="py-2" x-text="o.customer"></td>
                                <td class="py-2" x-text="formatJPY(o.total)"></td>
                                <td class="py-2">
                  <span class="px-2 py-1 rounded-md text-xs"
                        :class="{
                      'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200': o.status==='paid',
                      'bg-amber-50 text-amber-700 ring-1 ring-amber-200': o.status==='pending',
                      'bg-rose-50 text-rose-700 ring-1 ring-rose-200': o.status==='refunded'
                    }" x-text="statusLabel(o.status)"></span>
                                </td>
                                <td class="py-2 text-slate-500" x-text="o.datetime"></td>
                            </tr>
                        </template>
                        </tbody>
                    </table>
                </div>

                <!-- ページネーション -->
                <div class="mt-3 flex items-center justify-between text-sm">
                    <p class="text-slate-500" x-text="pageText()"></p>
                    <div class="flex gap-2">
                        <button class="rounded-md border px-3 py-1" :disabled="page===1" @click="page--">前へ</button>
                        <button class="rounded-md border px-3 py-1" :disabled="page>=maxPage()" @click="page++">次へ</button>
                    </div>
                </div>
            </div>

            <!-- アクティビティ -->
            <div class="rounded-md border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-slate-900">最近のアクティビティ</h3>
                    <button class="text-sm rounded-md border px-3 py-1" @click="openActivity()">全て表示</button>
                </div>
                <ol class="mt-3 space-y-3">
                    <li class="flex gap-3">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 mt-2"></span>
                        <div class="flex-1">
                            <p class="text-sm"><span class="font-medium">#A1024</span> が支払い済みに更新されました。</p>
                            <p class="text-xs text-slate-500">5分前</p>
                        </div>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-2 h-2 rounded-full bg-sky-500 mt-2"></span>
                        <div class="flex-1">
                            <p class="text-sm">新規ユーザー <span class="font-medium">Sato</span> を作成しました。</p>
                            <p class="text-xs text-slate-500">22分前</p>
                        </div>
                    </li>
                    <li class="flex gap-3">
                        <span class="w-2 h-2 rounded-full bg-amber-500 mt-2"></span>
                        <div class="flex-1">
                            <p class="text-sm">在庫警告：SKU-9981 が閾値を下回りました。</p>
                            <p class="text-xs text-slate-500">1時間前</p>
                        </div>
                    </li>
                </ol>
            </div>
        </div>

        <!-- タスク（簡易カンバン） -->
        <div class="rounded-md border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <h3 class="font-semibold text-slate-900">タスクボード</h3>
                <div class="flex gap-2">
                    <input type="text" class="form-input h-9 w-56" placeholder="新規タスク…" x-model="newTaskTitle" @keydown.enter.prevent="addTask()">
                    <button class="rounded-md bg-slate-900 text-white px-3 py-2 text-sm hover:bg-slate-800" @click="addTask()">追加</button>
                </div>
            </div>
            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                <template x-for="col in columns" :key="col.key">
                    <div class="rounded-md border border-slate-200">
                        <div class="px-3 py-2 border-b bg-slate-50 font-medium" x-text="col.title"></div>
                        <div class="p-3 space-y-2 min-h-[120px]">
                            <template x-for="(t, i) in tasks[col.key]" :key="t.id">
                                <div class="rounded-md border border-slate-200 p-3 bg-white flex items-start justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-slate-900" x-text="t.title"></p>
                                        <p class="text-xs text-slate-500 mt-1" x-text="t.meta"></p>
                                    </div>
                                    <div class="flex gap-1">
                                        <button class="text-xs rounded-md border px-2 py-1" @click="moveTask(col.key, i, -1)">←</button>
                                        <button class="text-xs rounded-md border px-2 py-1" @click="moveTask(col.key, i, 1)">→</button>
                                        <button class="text-xs rounded-md border px-2 py-1 text-rose-600" @click="delTask(col.key, i)">×</button>
                                    </div>
                                </div>
                            </template>
                            <div x-show="!tasks[col.key].length" class="text-xs text-slate-400">タスクなし</div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- 通知ドロワー（右） -->
        <div class="fixed inset-0 z-[60]" x-show="notifyOpen" style="display:none">
            <div class="absolute inset-0 bg-black/30" @click="notifyOpen=false"></div>
            <aside class="absolute right-0 top-0 bottom-0 w-full sm:w-[400px] bg-white border-l border-slate-200 shadow-xl"
                   x-transition:enter="transition ease-out duration-200"
                   x-transition:enter-start="translate-x-full"
                   x-transition:enter-end="translate-x-0"
                   x-transition:leave="transition ease-in duration-150"
                   x-transition:leave-start="translate-x-0"
                   x-transition:leave-end="translate-x-full">
                <div class="p-4 flex items-center justify-between border-b">
                    <h4 class="font-semibold">通知</h4>
                    <button class="rounded-md border px-3 py-1 text-sm" @click="notifyOpen=false">閉じる</button>
                </div>
                <div class="p-4 space-y-3 text-sm">
                    <div class="rounded-md border border-slate-200 p-3">
                        <p class="font-medium">メンテナンス予定</p>
                        <p class="text-slate-600 mt-1">9/12 02:00-03:00 に一部機能が停止します。</p>
                    </div>
                    <div class="rounded-md border border-slate-200 p-3">
                        <p class="font-medium">バージョン更新</p>
                        <p class="text-slate-600 mt-1">管理画面 v1.3.0 をリリースしました。</p>
                    </div>
                </div>
            </aside>
        </div>

        <!-- 新規登録モーダル -->
        <div class="fixed inset-0 z-[70]" x-show="newOpen" style="display:none">
            <div class="absolute inset-0 bg-black/30" @click="newOpen=false"></div>
            <div class="absolute inset-0 grid place-items-center p-4">
                <div class="w-full max-w-lg rounded-md border border-slate-200 bg-white shadow-xl"
                     x-transition>
                    <div class="p-4 border-b flex items-center justify-between">
                        <h4 class="font-semibold">新規エンティティ作成（ダミー）</h4>
                        <button class="rounded-md border px-3 py-1 text-sm" @click="newOpen=false">閉じる</button>
                    </div>
                    <div class="p-4 space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-slate-700">名称</label>
                            <input type="text" class="form-input mt-1" placeholder="例）9月キャンペーン">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">種別</label>
                            <select class="form-input mt-1">
                                <option>キャンペーン</option>
                                <option>ユーザー</option>
                                <option>商品</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">メモ</label>
                            <textarea rows="4" class="form-input mt-1" placeholder="自由入力"></textarea>
                        </div>
                    </div>
                    <div class="p-4 border-t flex items-center justify-end gap-2">
                        <button class="rounded-md border px-3 py-2 text-sm" @click="newOpen=false">キャンセル</button>
                        <button class="rounded-md bg-slate-900 text-white px-3 py-2 text-sm hover:bg-slate-800" @click="newOpen=false">作成</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 右上アクション -->
        <div class="fixed bottom-6 right-6 z-50">
            <div class="flex flex-col gap-2">
                <button class="rounded-full shadow-md border bg-white w-12 h-12 grid place-items-center hover:bg-slate-50" @click="notifyOpen=true" title="通知">
                    <svg class="w-5 h-5 text-slate-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M14.25 18.75a2.25 2.25 0 11-4.5 0m9-2.25H5.25l1.5-2.25v-3a5.25 5.25 0 0110.5 0v3l1.5 2.25z"/></svg>
                </button>
                <button class="rounded-full shadow-md border bg-white w-12 h-12 grid place-items-center hover:bg-slate-50" @click="scrollTo(0,0)" title="トップへ">
                    <svg class="w-5 h-5 text-slate-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75L12 8.25l7.5 7.5"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Alpine ロジック --}}
    <script>
        function dashboard(){
            return {
                // 状態
                range: '30d',
                from: '',
                to: '',
                q: '',
                status: '',
                perPage: 10,
                page: 1,
                sortKey: 'id',
                sortAsc: false,
                notifyOpen: false,
                newOpen: false,
                newTaskTitle: '',
                series: 'revenue',
                labels: ['月','火','水','木','金','土','日'],
                maxY: 120000, // グラフの最大値（ダミー）

                // ダミーデータ
                data: {
                    revenue: [32000, 48000, 51000, 60000, 72000, 80000, 94000],
                    orders:  [30,    42,    39,    50,    61,    70,    86   ]
                },
                orders: [
                    {id:'#A1024', customer:'田中', total:19800, status:'paid',     datetime:'2025-09-02 10:12'},
                    {id:'#A1025', customer:'佐藤', total:9800,  status:'pending',  datetime:'2025-09-02 10:21'},
                    {id:'#A1026', customer:'鈴木', total:42800, status:'paid',     datetime:'2025-09-02 10:33'},
                    {id:'#A1027', customer:'山本', total:1200,  status:'refunded', datetime:'2025-09-02 10:48'},
                    {id:'#A1028', customer:'高橋', total:15800, status:'paid',     datetime:'2025-09-02 11:06'},
                    {id:'#A1029', customer:'伊藤', total:6600,  status:'pending',  datetime:'2025-09-02 11:18'},
                    {id:'#A1030', customer:'渡辺', total:22400, status:'paid',     datetime:'2025-09-02 11:37'},
                    {id:'#A1031', customer:'中村', total:9900,  status:'paid',     datetime:'2025-09-02 11:55'},
                    {id:'#A1032', customer:'小林', total:38800, status:'paid',     datetime:'2025-09-02 12:07'},
                    {id:'#A1033', customer:'加藤', total:4800,  status:'pending',  datetime:'2025-09-02 12:24'},
                    {id:'#A1034', customer:'吉田', total:24800, status:'paid',     datetime:'2025-09-02 12:39'},
                ],
                columns: [
                    { key:'todo', title:'ToDo' },
                    { key:'doing', title:'進行中' },
                    { key:'done', title:'完了' },
                ],
                tasks: {
                    todo:  [{id:1,title:'商品Aの在庫確認',meta:'SKU-201'}],
                    doing: [{id:2,title:'キャンペーンLP修正',meta:'担当: Sato'}],
                    done:  [{id:3,title:'配送業者の契約更新',meta:'完了: 9/1'}],
                },

                // 初期化
                init(){},

                // UIヘルパ
                btnRange(key){ return (this.range===key ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50') },
                tabBtn(active){ return active ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50' },
                delta(v){ return (v>0? '+'+v: v)+'%' },
                deltaClass(v){ return v>=0? 'text-emerald-600':'text-rose-600' },
                formatJPY(n){ return '¥'+ n.toLocaleString() },

                // 折れ線ポイント生成
                chartPoints(key){
                    const arr = this.data[key];
                    const maxY = (key==='revenue') ? this.maxY : 120; // orders はスケール別
                    return arr.map((v, i)=>{
                        const x = 40 + i*100; // ラベル間隔
                        const y = 260 - (v/maxY)*220;
                        return `${x},${y}`
                    }).join(' ');
                },

                // テーブル関連
                statusLabel(s){ return {paid:'支払い済み', pending:'保留', refunded:'返金'}[s] || s },
                sortBy(k){
                    if(this.sortKey===k){ this.sortAsc=!this.sortAsc } else { this.sortKey=k; this.sortAsc=true }
                },
                pageText(){
                    const total = this.filteredOrders(true).length;
                    const start = (this.page-1)*this.perPage + 1;
                    const end = Math.min(this.page*this.perPage, total);
                    return `${start} - ${end} / ${total} 件`;
                },
                maxPage(){
                    const total = this.filteredOrders(true).length;
                    return Math.max(1, Math.ceil(total/this.perPage));
                },
                filteredOrders(rawCount=false){
                    let arr = [...this.orders];
                    if(this.q){
                        const s = this.q.toLowerCase();
                        arr = arr.filter(o => (o.id+o.customer).toLowerCase().includes(s))
                    }
                    if(this.status){ arr = arr.filter(o => o.status===this.status) }
                    // sort
                    arr.sort((a,b)=>{
                        if(a[this.sortKey] < b[this.sortKey]) return this.sortAsc? -1: 1;
                        if(a[this.sortKey] > b[this.sortKey]) return this.sortAsc? 1: -1;
                        return 0;
                    });
                    if(rawCount) return arr;
                    // pagination
                    const start = (this.page-1)*this.perPage;
                    const end = start + Number(this.perPage);
                    const sliced = arr.slice(start, end);
                    // ページがオーバーしたら戻す
                    const mp = Math.max(1, Math.ceil(arr.length/this.perPage));
                    if(this.page>mp){ this.page=mp }
                    return sliced;
                },

                // タスク操作
                addTask(){
                    const t = (this.newTaskTitle||'').trim();
                    if(!t) return;
                    this.tasks.todo.unshift({id:Date.now(), title:t, meta:'新規'});
                    this.newTaskTitle='';
                },
                moveTask(col, idx, dir){
                    const order = ['todo','doing','done'];
                    const cur = order.indexOf(col);
                    const next = cur + dir;
                    if(next<0 || next>=order.length) return;
                    const item = this.tasks[col].splice(idx,1)[0];
                    this.tasks[order[next]].unshift(item);
                },
                delTask(col, idx){ this.tasks[col].splice(idx,1) },

                // その他
                exportCSV(){
                    // ダミー：本実装時はサーバーAPIへ
                    alert('CSVをエクスポート（ダミー）');
                },
                openNew(){ this.newOpen = true },
                openActivity(){ this.notifyOpen = true },
            }
        }
    </script>
@endsection
