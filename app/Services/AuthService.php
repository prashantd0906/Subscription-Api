<?php
namespace App\Services;

use App\Interfaces\AuthRepositoryInterface;

class AuthService
{
    public function __construct(protected AuthRepositoryInterface $authRepo) {}

    public function register(array $data)
    {
        return $this->authRepo->register($data);
    }

    public function login(array $credentials)
    {
        return $this->authRepo->login($credentials);
    }

    public function logout()
    {
        return $this->authRepo->logout();
    }

    public function me()
    {
        return $this->authRepo->me();
    }
}
