<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VolunteerJobsTaken extends Model
{
    
    use HasFactory;
    protected $fillable = ['id', 'job_id', 'taken_by', 'status']; 

    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id')->with(['user_id', 'address', 'tree']);
    }
    public function proofs()
    {
        return $this->hasMany(JobsTakenProof::class, 'transaction_id', 'id');
    } 

    public function tree()
    {
        return $this->belongsTo(Tree::class, 'job_id')->with(['name', 'tree_value']);
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'taken_by');
    } 

}
