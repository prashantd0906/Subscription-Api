<?php
namespace App\Services;

use App\Interfaces\PromoCodeRepositoryInterface;

class PromoCodeService
{
    public function __construct(private readonly PromoCodeRepositoryInterface $promoCodeRepo) {}

    public function createPromoCode(array $data)
    {
        return $this->promoCodeRepo->create($data);
    }

    public function validatePromoCode(string $code)
    {
        $promo = $this->promoCodeRepo->findByCode($code);

        if (!$promo || now()->greaterThan($promo->valid_till)) {
            return null;
        }

        return $promo;
    }
}
