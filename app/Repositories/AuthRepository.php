<?php

namespace App\Repositories;

use App\Interfaces\AuthRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthRepository implements AuthRepositoryInterface
{
    public function register(array $data)
    {
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id'  => 1,
        ]);

        return $user;
    }

    public function login(array $credentials)
    {
        if (!$token = JWTAuth::attempt($credentials)) {
            return null;
        }

        $user = JWTAuth::user();
        return compact('user', 'token');
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return true;
    }

    public function me()
    {
        return JWTAuth::user();
    }
}
