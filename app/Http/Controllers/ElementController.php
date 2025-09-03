<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreElementRequest;
use App\Http\Requests\UpdateElementRequest;
use App\Models\Element;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ElementController extends Controller
{
    //
    public function index()
    {
        $elements = Element::orderBy('title')->get();
        return view('elements.index', compact('elements'));
    }

    public function store(StoreElementRequest $request)
    {
        Element::create([
            'title'    => $request->string('title'),
            'visible' => (int)$request->input('visible'),
        ]);

        return redirect()->route('elements.index')
            ->with('success', 'カテゴリを登録しました。');
    }

    public function update(UpdateElementRequest $request, Element $element) // ← $elements → $element に
    {
        $element->update([
            'title'   => $request->input('title'),
            'visible' => (int) $request->input('visible'),
        ]);

        return response()->json([
            'ok'   => true,
            'element' => $element->refresh(),
        ]);
    }


    public function destroy(Request $request, Element $elements)
    {
        $elements->delete();
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
            'title' => ['required', 'string', 'max:100', 'unique:elements,title'],
        ], [], [
            'title' => 'シナリオ種別',
        ]);

        $element = Element::create([
            'title'   => $validated['title'],
            'visible' => 1,
        ]);

        return response()->json([
            'ok'   => true,
            'element' => ['id' => $element->id, 'title' => $element->title],
        ]);
    }

}
