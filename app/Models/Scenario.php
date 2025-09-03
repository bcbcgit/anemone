<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scenario extends Model
{
    /** @use HasFactory<\Database\Factories\ScenarioFactory> */
    use HasFactory;

    protected $fillable = ['title','url','body','visible','memo','image'];

    public function kinds()
    {
        return $this->belongsToMany(Kind::class)->withTimestamps();
        // 別名にしたなら ->belongsToMany(Kind::class, 'kinds_scenarios')
    }

    public function elements()
    {
        return $this->belongsToMany(Element::class)->withTimestamps();
    }
}
