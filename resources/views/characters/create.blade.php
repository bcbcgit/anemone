{{-- resources/views/characters/create.blade.php --}}
@extends('layouts.admin')

@section('title','キャラクター登録')
@section('page_title','キャラクター登録')

@section('content')
    <div class="max-w-md mx-auto">
        @if ($errors->any())
            <div class="p-4 mb-4 border border-red-300 bg-red-50 rounded">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li class="text-red-700">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="post" action="{{ route('characters.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700">キャラクター名</label>
                <input type="text" name="name" maxlength="100" required
                       class="mt-1 block w-full border rounded px-3 py-2">
            </div>

            <div class="flex justify-end">
                <button class="px-4 py-2 rounded bg-slate-900 text-white hover:bg-slate-800">
                    登録
                </button>
            </div>
        </form>
    </div>
@endsection
