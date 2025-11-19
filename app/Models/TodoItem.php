<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TodoItem extends Model
{
    use HasFactory;

    protected $table = 'items';

    protected $fillable = [
        'list_id',
        'title',
        'description',
        'completed'
    ];

    public function list()
    {
        return $this->belongsTo(TodoList::class, 'list_id');
    }
}
