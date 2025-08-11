<?php
namespace App\Repositories;

use App\Interfaces\PromoCodeRepositoryInterface;
use App\Models\PromoCode;

class PromoCodeRepository implements PromoCodeRepositoryInterface
{
    public function create(array $data)
    {
        return PromoCode::create($data);
    }

    public function findByCode(string $code)
    {
        return PromoCode::where('code', $code)->first();
    }
}