<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobsTakenProof extends Model
{
    use HasFactory;

    protected $table = 'jobs_taken_proof';

    protected $fillable = [
        'id',
        'proof', 
        'transaction_id', 
        'created_at', 
        'updated_at',
    ];
} 