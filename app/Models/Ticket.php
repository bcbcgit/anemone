<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    //
    protected $fillable = ['character_id','status','used_at'];
    protected $casts = ['used_at' => 'datetime'];
    public function character(): BelongsTo {
        return $this->belongsTo(Character::class);
    }
}
