<?php

namespace App\Repositories;

use App\Models\User;
use DragonCode\Support\Facades\Helpers\Boolean;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    public function GetAllUsers()
    {
        return User::all();
    }

    public function findByEmail(string $email)
    {
        return User::where('email', $email)->first();
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

    public function destroy(): User
    {
        $user =  User::findOrFail(auth()->user()->id);
        $user->delete();
        return $user;
    }
}
