<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact_type extends Model
{
    use HasFactory;

    protected $table = 'contact_types';

    protected $fillable = ['type_english', 'type_arabic'];

    public function contact_information()
    {
        return $this->belongsTo(Contact_information::class);
    }
}
