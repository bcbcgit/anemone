<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Ticket;

class Character extends Model
{
    //
    protected $fillable = ['user_id','name','points_total'];
    public function tickets(): HasMany {
        return $this->hasMany(Ticket::class);
    }
    public function scopeMine($q) {
        return $q->where('user_id', auth()->id());
    }
}
