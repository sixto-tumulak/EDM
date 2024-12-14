<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tree extends Model
{
    public function tree()
    {
        return $this->belongsTo(Tree::class, 'id'); 
    }


    use HasFactory;
    protected $fillable = ['name', 'tree_value']; 
}
