<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PromoCode;
use Carbon\Carbon;

class PromoCodeSeeder extends Seeder
{
    public function run(): void
    {
        $promoCodes = [
            [
                'code' => 'WELCOME10',
                'discount' => 10,
                'valid_till' => Carbon::now()->addMonths(1),
            ],
            [
                'code' => 'SUMMER50',
                'discount' => 50,
                'valid_till' => Carbon::now()->addMonths(2),
            ],
            [
                'code' => 'FREEMONTH',
                'discount' => 100,
                'valid_till' => Carbon::now()->addMonths(3),
            ],
        ];

        foreach ($promoCodes as $promo) {
            PromoCode::create($promo);
        }
    }
}
