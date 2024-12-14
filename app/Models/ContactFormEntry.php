<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactFormEntry extends Model
{
    protected $table = 'contact_form_entries';

    protected $fillable = ['name', 'email', 'message'];
}