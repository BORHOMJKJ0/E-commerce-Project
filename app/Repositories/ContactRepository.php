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
        return DB::table('contact_informations')->where('user_id', $id)->select('id', 'link', 'user_id', 'contact_type_id')->get();
    }

    public function findById($id)
    {
        return DB::table('contact_informations')->where('id', $id)->select('id', 'link', 'user_id', 'contact_type_id')->first();
    }

    public function deleteById($id)
    {
        $contact = $this->findById($id);
        DB::table('contact_informations')->where('id', $id)->delete();

        return $contact;
    }

    public function deleteByUser($user)
    {
        $contact = $this->findByUserId($user->id);
        DB::table('contact_informations')->where('user_id', $user->id)->delete();

        return $contact;
    }
}
