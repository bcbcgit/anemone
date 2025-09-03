<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kind extends Model
{
    //
    protected $fillable = ['title','visible'];
    protected $casts = ['visible'=>'boolean'];

    public function scenarios()
    {
        return $this->belongsToMany(Scenario::class)->withTimestamps();
    }
}
