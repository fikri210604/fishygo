<?php

namespace Tests\Feature;

use App\Models\Alamat;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PesananApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    protected function createUserWithAlamat(): array
    {
        $user = User::factory()->create();
        $alamat = Alamat::factory()->create(['pengguna_id' => $user->id]);
        return [$user, $alamat];
    }

    public function test_checkout_create_page_requires_auth(): void
    {
        $response = $this->get('/checkout');
        $response->assertRedirect();
    }

    public function test_can_create_order_from_cart(): void
    {
        [$user, $alamat] = $this->createUserWithAlamat();

        $produk1 = Produk::factory()->create(['harga' => 10000, 'harga_promo' => null, 'stok' => 10]);
        $produk2 = Produk::factory()->create(['harga' => 25000, 'harga_promo' => null, 'stok' => 5]);

        $cart = [
            $produk1->produk_id => ['qty' => 2], // 2 x 10000 = 20000
            $produk2->produk_id => ['qty' => 1], // 1 x 25000 = 25000
        ];

        $this->actingAs($user)
            ->withSession(['cart' => $cart]);

        $res = $this->post('/checkout', [
            'alamat_id' => $alamat->id,
            'metode_pembayaran' => 'manual',
            'catatan' => 'tolong cepat',
        ]);

        $res->assertRedirect();

        $this->assertDatabaseHas('pesanan', [
            'pengguna_id' => $user->id,
            'subtotal' => 45000.00,
            'status' => Pesanan::STATUS_MENUNGGU_PEMBAYARAN,
        ]);

        $order = Pesanan::first();
        $this->assertNotNull($order);
        $this->assertEquals(2, $order->items()->count());

        // Stok berkurang
        $this->assertEquals(8, $produk1->fresh()->stok);
        $this->assertEquals(4, $produk2->fresh()->stok);

        // Pembayaran dibuat
        $this->assertDatabaseHas('pembayaran', [
            'pesanan_id' => $order->pesanan_id,
            'status' => 'pending',
        ]);
    }

    public function test_user_can_cancel_waiting_payment_order(): void
    {
        [$user, $alamat] = $this->createUserWithAlamat();
        $produk = Produk::factory()->create(['harga' => 12000, 'harga_promo' => null, 'stok' => 3]);

        // Buat pesanan via web flow
        $cart = [ $produk->produk_id => ['qty' => 2] ];
        $this->actingAs($user)->withSession(['cart' => $cart]);
        $this->post('/checkout', ['alamat_id' => $alamat->id]);

        $order = Pesanan::first();
        $this->assertNotNull($order);
        $this->assertEquals(Pesanan::STATUS_MENUNGGU_PEMBAYARAN, $order->status);

        // Cancel
        $res = $this->actingAs($user)->post('/pesanan/'.$order->pesanan_id.'/cancel', [
            'reason' => 'change_mind',
            'note' => 'salah order',
        ]);
        $res->assertRedirect();

        $order->refresh();
        $this->assertEquals(Pesanan::STATUS_DIBATALKAN, $order->status);
        $this->assertNotNull($order->cancelled_at);
        $this->assertEquals('change_mind', $order->cancel_reason);

        // Stok kembali
        $this->assertEquals(3, $produk->fresh()->stok);

        // Pembayaran dibatalkan
        $this->assertDatabaseHas('pembayaran', [
            'pesanan_id' => $order->pesanan_id,
            'status' => 'cancelled',
        ]);
    }
}
