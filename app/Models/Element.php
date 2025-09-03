<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Element extends Model
{
    //
    protected $fillable = ['title'];

    public function scenarios()
    {
        return $this->belongsToMany(Scenario::class)->withTimestamps();
    }
}
