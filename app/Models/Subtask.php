<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subtask extends Model
{
    protected $fillable = ["title","isCompleted","task_id"];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function task(){
        return $this->belongsTo(Task::class);
    }
}
