<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\TodoList;
class TodoElement extends Model
{
    use HasFactory;

    protected $fillable = [];
    public $timestamps = false;
    
    public function todoList()
    {
        return $this->belongsTo(TodoList::class);
    }
}
