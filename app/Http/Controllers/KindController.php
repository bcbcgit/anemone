<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKindRequest;
use App\Http\Requests\UpdateKindRequest;
use App\Models\Kind;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class KindController extends Controller
{
    //
    public function index()
    {
        $kinds = Kind::orderBy('title')->get();
        return view('kinds.index', compact('kinds'));
    }

    public function store(StoreKindRequest $request)
    {
        Kind::create([
            'title'    => $request->string('title'),
            'visible' => (int)$request->input('visible'),
        ]);

        return redirect()->route('kinds.index')
            ->with('success', 'カテゴリを登録しました。');
    }

    public function update(UpdateKindRequest $request, Kind $kind) // ← $kinds → $kind に
    {
        $kind->update([
            'title'   => $request->input('title'),
            'visible' => (int) $request->input('visible'),
        ]);

        return response()->json([
            'ok'   => true,
            'kind' => $kind->refresh(),
        ]);
    }


    public function destroy(Request $request, Kind $kinds)
    {
        $kinds->delete();
        return response()->json(['ok'=>true]);
    }

    // シナリオ登録時に新規でカテゴリーを追加する
    public function inlineStore(Request $request)
    {
        // 前後空白・連続空白の正規化（同一視のため）
        $title = Str::of($request->input('title', ''))
            ->trim()
            ->replaceMatches('/\s+/u', ' ')
            ->toString();

        $validated = $request->merge(['title' => $title])->validate([
            'title' => ['required', 'string', 'max:100', 'unique:kinds,title'],
        ], [], [
            'title' => 'シナリオ種別',
        ]);

        $kind = Kind::create([
            'title'   => $validated['title'],
            'visible' => 1,
        ]);

        return response()->json([
            'ok'   => true,
            'kind' => ['id' => $kind->id, 'title' => $kind->title],
        ]);
    }

}
