<?php

namespace App\Interfaces;

use App\Models\PromoCode;

interface PromoCodeRepositoryInterface
{
    public function create(array $data);
    public function find(int $id);
    public function findByCode(string $code);
    public function update(PromoCode $promo, array $data); 
    public function delete(PromoCode $promo);              
}
