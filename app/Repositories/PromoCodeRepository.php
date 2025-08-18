<?php

namespace App\Repositories;

use App\Interfaces\PromoCodeRepositoryInterface;
use App\Models\PromoCode;

class PromoCodeRepository implements PromoCodeRepositoryInterface
{
    public function __construct(protected PromoCode $model) {}

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function find(int $id)
    {
        return $this->model->find($id);
    }

    public function findByCode(string $code)
    {
        return PromoCode::whereRaw('LOWER(code) = ?', [strtolower($code)])->first();
    }

    public function update(PromoCode $promo, array $data)
    {
        $promo->update($data);
        return $promo;
    }

    public function delete(PromoCode $promo)
    {
        $promo->delete();
    }
}
