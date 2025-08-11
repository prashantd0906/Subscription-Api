<?php
namespace App\Interfaces;

interface PromoCodeRepositoryInterface
{
    public function create(array $data);
    public function findByCode(string $code);
}
