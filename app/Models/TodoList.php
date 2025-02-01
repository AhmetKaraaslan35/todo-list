<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\TodoElement;


class TodoList extends Model
{
    use HasFactory;

    protected $fillable = [];
    public $timestamps = false;
    
    public function todoElements()
    {
        return $this->hasMany(TodoElement::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
