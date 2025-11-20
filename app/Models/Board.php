<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    protected $fillable = ["name"];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function columns() {
        return $this->hasMany(Column::class);
    }
}
