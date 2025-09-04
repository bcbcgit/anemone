<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CharactersMemoController extends Controller
{
    //
    private function memoPath(int $userId): string
    {
        // ユーザー単位で分離
        return "memos/characters/{$userId}.txt";
    }

    public function show(Request $request): JsonResponse
    {
        $path = $this->memoPath($request->user()->id);

        $exists = Storage::disk('local')->exists($path);
        $content = $exists ? Storage::disk('local')->get($path) : '';
        $updatedAt = $exists ? Storage::disk('local')->lastModified($path) : null;

        return response()->json([
            'content' => $content,
            'updated_at' => $updatedAt ? date('Y-m-d H:i:s', $updatedAt) : null,
        ]);
    }

    public function save(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'content' => ['nullable','string','max:20000'], // 上限はお好みで
        ]);

        $path = $this->memoPath($request->user()->id);

        // ディレクトリが無ければ作成
        Storage::disk('local')->makeDirectory(dirname($path));

        Storage::disk('local')->put($path, $validated['content'] ?? '');

        $updatedAt = Storage::disk('local')->lastModified($path);

        return response()->json([
            'ok' => true,
            'updated_at' => date('Y-m-d H:i:s', $updatedAt),
        ]);
    }
}
