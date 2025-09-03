{{-- resources/views/scenarios/show.blade.php --}}
@extends('layouts.admin')

@section('title', 'シナリオ詳細')
@section('page_title', 'シナリオ詳細')
@section('page_desc', 'シナリオ名、URL、本文、表示/非表示、シナリオ種別、メモ、メイン画像の確認')

@section('content')
    <div class="space-y-6">
        <div class="grid grid-cols-2 gap-6">
            {{-- 左カラム --}}
            <div class="space-y-6">
                {{-- メイン画像 --}}
                <div class="rounded-md border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="block text-sm font-medium text-slate-700">メイン画像</div>
                    @if(!empty($scenario->image))
                        <div class="mt-2">
                            <img src="{{ asset('storage/'.$scenario->image) }}" alt="main image"
                                 class="max-w-full w-64 h-64 object-cover rounded-md ring-1 ring-slate-200 bg-slate-50">
                        </div>
                    @else
                        <div class="mt-1 text-slate-500">未設定</div>
                    @endif
                </div>

                {{-- シナリオ名 --}}
                <div class="rounded-md border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="block text-sm font-medium text-slate-700">シナリオ名</div>
                    <div class="mt-1 text-slate-900">{{ $scenario->title }}</div>
                </div>

                {{-- タグ --}}
                <div class="rounded-md border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="block text-sm font-medium text-slate-700">シナリオ種別</div>
                    @php $kinds = optional($scenario->kinds) ?? collect(); @endphp
                    @if($kinds->isNotEmpty())
                        <div class="mt-1 flex flex-wrap gap-2">
                            @foreach($kinds as $kind)
                                <span class="inline-flex items-center gap-2 px-2 py-1 rounded-md ring-1 ring-slate-200 bg-slate-50 text-sm">
                                    {{ $kind->title }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <div class="mt-1 text-slate-500">なし</div>
                    @endif
                </div>

                {{-- 日付 --}}
                <div class="rounded-md border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="block text-sm font-medium text-slate-700">日付</div>
                    <div class="mt-1 text-slate-900">{{ $scenario->created_at?->format('Y-m-d H:i') }}</div>
                </div>
            </div>

            {{-- 右カラム --}}
            <div class="space-y-6">
                {{-- シナリオ本文 --}}
                <div class="rounded-md border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="block text-sm font-medium text-slate-700">シナリオ本文</div>
                    @if(!empty($scenario->body))
                        <div class="prose prose-slate max-w-none mt-2 text-slate-900 leading-7">
                            {!! nl2br(e($scenario->body)) !!}
                        </div>
                    @else
                        <div class="mt-1 text-slate-500">未入力</div>
                    @endif
                </div>

                {{-- メモ --}}
                <div class="rounded-md border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="block text-sm font-medium text-slate-700">メモ</div>
                    @if(!empty($scenario->memo))
                        <div class="mt-2 text-slate-900 whitespace-pre-wrap leading-7">{{ $scenario->memo }}</div>
                    @else
                        <div class="mt-1 text-slate-500">未入力</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- アクション --}}
        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('scenarios.index') }}" class="rounded-md border border-slate-200 bg-white px-4 py-2 text-sm hover:bg-slate-50">一覧へ戻る</a>
            <a href="{{ route('scenarios.edit', $scenario) }}" class="rounded-md bg-slate-900 text-white px-4 py-2 text-sm hover:bg-slate-800">編集する</a>
        </div>
    </div>
@endsection
