<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Column extends Model
{
    protected $fillable = ["name","color","board_id"];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function board(){
        return $this->belongsTo(Board::class);
    }
    public function tasks(){
        return $this->hasMany(Task::class);
    }
}
