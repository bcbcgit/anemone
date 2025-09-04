<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CharacterPointController extends Controller
{
    //
    public function index(Request $request) {
        $characters = Character::mine()
            ->withCount(['tickets as tickets_unused_count' => function($q){ $q->where('status','unused'); }])
            ->orderBy('id')
            ->get();

        return view('characters.index', compact('characters'));
    }

    public function increment(Request $request, Character $character): JsonResponse
    {
        // 所有権チェック
        abort_unless($character->user_id === $request->user()->id, 403);

        $data = DB::transaction(function () use ($character) {
            // 行ロックで現在値を固定
            $c = Character::whereKey($character->id)->lockForUpdate()->first();

            $c->points_total += 1;
            $issued = false;

            if ($c->points_total % 50 === 0) {
                Ticket::create([
                    'character_id' => $c->id,
                    'status' => 'unused',
                ]);
                $issued = true;
            }
            $c->save();

            $unused = Ticket::where('character_id', $c->id)->where('status','unused')->count();

            return [
                'character_id' => $c->id,
                'points_total' => $c->points_total,
                'tickets_unused' => $unused,
                'ticket_issued' => $issued,
            ];
        });

        return response()->json($data);
    }
}
