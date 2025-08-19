<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\PromoCode;
use App\Repositories\PromoCodeRepository;

class PromoCodeRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected PromoCodeRepository $repo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repo = new PromoCodeRepository(new PromoCode());
    }

    public function it_can_create_a_promo_code()
    {
        $data = [
            'code' => 'SAVE10',
            'discount' => 10,
            'expires_at' => now()->addDays(7),
        ];

        $promo = $this->repo->create($data);

        $this->assertDatabaseHas('promo_codes', ['code' => 'SAVE10']);
        $this->assertEquals('SAVE10', $promo->code);
    }

    public function it_can_find_a_promo_code_by_id()
    {
        $promo = PromoCode::factory()->create();

        $found = $this->repo->find($promo->id);

        $this->assertNotNull($found);
        $this->assertEquals($promo->id, $found->id);
    }

    public function it_can_find_a_promo_code_by_code_case_insensitive()
    {
        PromoCode::factory()->create(['code' => 'SUMMER50']);

        $found = $this->repo->findByCode('summer50');

        $this->assertNotNull($found);
        $this->assertEquals('SUMMER50', $found->code);
    }

    public function it_can_update_a_promo_code()
    {
        $promo = PromoCode::factory()->create(['code' => 'OLD10']);

        $updated = $this->repo->update($promo, [
            'code' => 'NEW10',
            'discount' => 15,
        ]);

        $this->assertEquals('NEW10', $updated->code);
        $this->assertDatabaseHas('promo_codes', ['code' => 'NEW10']);
    }

    public function it_can_delete_a_promo_code()
    {
        $promo = PromoCode::factory()->create();

        $this->repo->delete($promo);

        $this->assertDatabaseMissing('promo_codes', ['id' => $promo->id]);
    }
}
