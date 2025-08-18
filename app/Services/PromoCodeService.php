<?php

namespace App\Services;

use App\Interfaces\PromoCodeRepositoryInterface;

class PromoCodeService
{
    public function __construct(private readonly PromoCodeRepositoryInterface $promoCodeRepo) {}

    public function createPromoCode(array $data)
    {
        return $this->promoCodeRepo->create([
            'code'       => $data['promo_code'],
            'discount'   => $data['discount'],
            'valid_till' => $data['valid_till'],
        ]);
    }

    public function validatePromoCode(string $code)
    {
        $promo = $this->promoCodeRepo->findByCode($code);

        if (!$promo || now()->greaterThan($promo->valid_till)) {
            return null;
        }

        return $promo;
    }

    public function updatePromoCode(int $id, array $data)
    {
        $promo = $this->promoCodeRepo->find($id);

        if (!$promo) {
            throw new \InvalidArgumentException("Promo code not found.");
        }

        return $this->promoCodeRepo->update($promo, [
            'code'       => $data['promo_code'],
            'discount'   => $data['discount'],
            'valid_till' => $data['valid_till'],
        ]);
    }
    public function deletePromoCode(int $id): void
    {
        $promo = $this->promoCodeRepo->find($id);

        if (!$promo) {
            throw new \InvalidArgumentException("Promo code not found.");
        }

        $this->promoCodeRepo->delete($promo);
    }
}
