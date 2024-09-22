<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    public function findByEmail(string $email)
    {
        return User::where('email', $email)->first();
    }

    public function get_user_contactById($id)
    {
        return User::where('id', $id)->GetUserWithContacts();
    }

    public function get_userById($id)
    {
        return User::where('id', $id)->select('id', 'name', 'email', 'mobile')->get();
    }

    public function get_users_contacts()
    {
        return User::orderBy('id')->GetUserWithContacts();
    }

    public function findById($id)
    {
        return User::where('id', $id)->first();
    }

    public function create(array $data)
    {
        $data['password'] = Hash::make($data['password']);

        return User::create($data);
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user;
    }

    public function VerifyEmail(User $user): User
    {
        $user->email_verified_at = now()->format('Y-m-d H:i:s');
        $user->save();

        return $user;
    }
}
