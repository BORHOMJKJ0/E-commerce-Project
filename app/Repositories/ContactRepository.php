<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ContactRepository
{
    public function create($user, array $data): Model
    {
        $data['user_id'] = $user->id;

        return $user->contacts()->create($data);
    }

    public function findByUserId($id)
    {
        return DB::table('contact_informations')->where('user_id', $id)->get();
    }

    public function findById($id)
    {
        return DB::table('contact_informations')->where('id', $id)->first();
    }

    public function deleteById($id)
    {
        return DB::table('contact_informations')->where('id', $id)->delete();
    }

    public function deleteByUser($user)
    {
        return DB::table('contact_informations')->where('user_id', $user->id)->delete();
    }
}
