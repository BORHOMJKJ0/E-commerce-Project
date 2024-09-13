<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class contact_information extends Model
{
    use HasFactory;

    protected $table = 'contact_informations';
    protected $fillable = [ 'user_id' , 'contact_type_id' , 'link'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function contact_type(){
        return $this->hasOne(contact_type::class);
    }
}
