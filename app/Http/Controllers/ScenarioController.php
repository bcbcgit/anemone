<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreScenarioRequest;
use App\Http\Requests\UpdateScenarioRequest;
use App\Models\Element;
use App\Models\Kind;
use App\Models\Scenario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Http\UploadedFile;

class ScenarioController extends Controller
{
    /** 一覧 */
    public function index()
    {
        // 種別は表示名順、シナリオはID降順で kinds を eager load
        $kinds = Kind::orderBy('title')->get();
        $elements = Element::orderBy('title')->get();
        $scenarios = Scenario::with('kinds:id,title','elements:id,title')
            ->where('visible', 1)
            ->latest('id')
            ->get();

        return view('scenarios.index', compact('kinds', 'elements','scenarios'));
    }

    /** 新規画面 */
    public function create()
    {
        $kinds = Kind::orderBy('title')->get();
        $elements = Element::orderBy('title')->get();
        return view('scenarios.create', compact('kinds','elements'));
    }

    /** 保存 */
    public function store(StoreScenarioRequest $request)
    {
        $savedPath = null;

        try {
            DB::beginTransaction();

            if ($request->hasFile('image')) {
                $savedPath = $this->saveResizedImage($request->file('image')); // 共通化
            }

            $scenario = Scenario::create([
                'title'   => $request->input('title'),
                'url'     => $request->input('url'),
                'body'    => $request->input('body'),
                'visible' => (int) $request->input('visible', 1),
                'memo'    => $request->input('memo'),
                'image'   => $savedPath,
            ]);

            $scenario->kinds()->sync($request->input('kinds', []));
            $scenario->elements()->sync($request->input('elements', []));

            DB::commit();
            return redirect()->route('scenarios.index')->with('success', 'シナリオを登録しました。');

        } catch (\Throwable $e) {
            DB::rollBack();
            if ($savedPath) Storage::disk('public')->delete($savedPath);
            report($e);
            return back()->withErrors('シナリオの保存に失敗しました。')->withInput();
        }
    }

    // 更新画像
    public function show(Scenario $scenario) {
        $kinds = Kind::orderBy('title')->get();
        $elements = Element::orderBy('title')->get();
        return view('scenarios.show', compact('scenario','kinds', 'elements'));
    }

    // 更新画像
    public function edit(Scenario $scenario) {
        $kinds = Kind::orderBy('title')->get();
        $elements = Element::orderBy('title')->get();
        return view('scenarios.edit', compact('scenario','kinds', 'elements'));
    }

    /** 更新（画像差し替え対応） */
    public function update(UpdateScenarioRequest $request, Scenario $scenario)
    {
        $newPath = null;
        $oldPath = $scenario->image;

        try {
            DB::beginTransaction();

            // 先に本文などを更新
            $scenario->update([
                'title'   => $request->input('title'),
                'url'     => $request->input('url'),
                'body'    => $request->input('body'),
                'visible' => (int) $request->input('visible', 1),
                'memo'    => $request->input('memo'),
            ]);

            // 画像の差し替え（あれば新規保存 → パス入れ替え）
            if ($request->hasFile('image')) {
                $newPath = $this->saveResizedImage($request->file('image'));
                $scenario->forceFill(['image' => $newPath])->save();
            }

            // 種別の同期
            $scenario->kinds()->sync($request->input('kinds', []));
            // 要素の同期
            $scenario->elements()->sync($request->input('elements', []));

            DB::commit();

            // 旧画像はコミット後に安全に削除
            if ($newPath && $oldPath) {
                Storage::disk('public')->delete($oldPath);
            }

            return redirect()->route('scenarios.index')->with('success', 'シナリオを更新しました。');

        } catch (\Throwable $e) {
            DB::rollBack();
            // 新規保存済みの画像はロールバック時に掃除
            if ($newPath) Storage::disk('public')->delete($newPath);
            report($e);
            return back()->withErrors('更新に失敗しました。もう一度お試しください。')->withInput();
        }
    }

    /** 削除（画像と関連の掃除） */
    public function destroy(Scenario $scenario)
    {
        try {
            DB::beginTransaction();

            $image = $scenario->image;

            // 関連の掃除（kinds / elements）
            $scenario->kinds()->detach();
            if (method_exists($scenario, 'elements')) {
                $scenario->elements()->detach();
            }

            $scenario->delete();

            DB::commit();

            // 画像ファイルはコミット後に削除
            if ($image) Storage::disk('public')->delete($image);

            return redirect()->route('scenarios.index')->with('success', 'シナリオを削除しました。');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->withErrors('削除に失敗しました。');
        }
    }

    /** 共通：アップロード画像を縮小して保存し、パスを返す */
    private function saveResizedImage(UploadedFile $file): string
    {
        $img = Image::read($file)->scaleDown(300, 300);
        $uuid = (string) Str::uuid();

        // 1) WebP
        try {
            $bin = $img->encodeByExtension('webp', quality: 75);
            $path = "scenarios/{$uuid}.webp";
            Storage::disk('public')->put($path, $bin);
            return $path;
        } catch (\Throwable $e) { /* 次へ */ }

        // 2) JPEG
        try {
            $bin = $img->encodeByExtension('jpg', quality: 82);
            $path = "scenarios/{$uuid}.jpg";
            Storage::disk('public')->put($path, $bin);
            return $path;
        } catch (\Throwable $e) { /* 次へ */ }

        // 3) PNG（最終手段：多くの環境で可）
        $bin = $img->encodeByExtension('png'); // 圧縮はライブラリ側に任せる
        $path = "scenarios/{$uuid}.png";
        Storage::disk('public')->put($path, $bin);
        return $path;
    }

}
