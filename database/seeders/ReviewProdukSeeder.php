<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produk;
use App\Models\User;
use App\Models\ReviewProduk;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReviewProdukSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role_slug', User::ROLE_USER)
            ->orWhereNull('role_slug')
            ->inRandomOrder()
            ->limit(50)
            ->get(['id']);

        if ($users->isEmpty()) return;

        $sentences = [
            'Produk bagus, sesuai deskripsi.',
            'Pengemasan rapi, pengiriman cepat.',
            'Kualitas oke dengan harga terjangkau.',
            'Cukup memuaskan, akan order lagi.',
            'Sesuai ekspektasi, recommended!',
            'Rasa enak dan segar.',
            'Pelayanan baik, respon cepat.',
            'Pengiriman sedikit lambat tapi produk aman.',
            'Kualitas perlu ditingkatkan.',
        ];

        $productCount = rand(10, 50);

        Produk::query()
            ->inRandomOrder()
            ->limit($productCount)
            ->get()
            ->each(function (Produk $produk) use ($users, $sentences) {
                $pickedUsers = $users->random(rand(3, min(8, $users->count())));

                $data = $pickedUsers->map(function ($user) use ($produk, $sentences) {
                    return [
                        'review_id'  => (string) Str::uuid(),
                        'produk_id'   => $produk->produk_id,
                        'pengguna_id' => $user->id,
                        'rating'      => rand(1, 5),
                        'review'      => Arr::random($sentences),
                        'created_at'  => now()->subDays(rand(1, 90)), 
                        'updated_at'  => now(),
                    ];
                });

                DB::table('review_produk')->upsert(
                    $data->toArray(),
                    ['produk_id', 'pengguna_id'], 
                    ['rating', 'review', 'updated_at']
                );

                $produk->recalcRating();
            });
    }
}
