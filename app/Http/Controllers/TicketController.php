<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    //
    public function useOne(Request $request, Character $character): JsonResponse
    {
        abort_unless($character->user_id === $request->user()->id, 403);

        $data = DB::transaction(function () use ($character) {
            // 未使用の最古をロック
            $ticket = Ticket::where('character_id', $character->id)
                ->where('status','unused')
                ->lockForUpdate()
                ->oldest('id')
                ->first();

            if (!$ticket) {
                return [
                    'character_id'   => $character->id,
                    'used'           => false,
                    'message'        => '未使用チケットがありません',
                    'tickets_unused' => 0,
                ];
            }

            $ticket->status = 'used';
            $ticket->used_at = now();
            $ticket->save();

            $unused = Ticket::where('character_id', $character->id)->where('status','unused')->count();

            return [
                'character_id'   => $character->id,
                'used'           => true,
                'tickets_unused' => $unused,
            ];
        });

        return response()->json($data, 200);
    }
}
