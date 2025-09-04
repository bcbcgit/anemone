<?php

namespace App\Http\Controllers;

use App\Models\Character;
use Illuminate\Http\Request;

class CharacterController extends Controller
{
    //
    public function create()
    {
        return view('characters.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required','string','max:100'],
        ]);

        Character::create([
            'user_id' => $request->user()->id,
            'name'    => $validated['name'],
            'points_total' => 0,
        ]);

        return redirect()->route('characters.index')
            ->with('success', 'キャラクターを登録しました');
    }
}
