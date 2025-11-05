<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Article;
use App\Models\User;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::where('email', 'admin@example.com')->first()
            ?: User::first();

        if (! $author) {
            return; // no users available
        }

        $items = [
            [
                'judul' => 'Panduan Memulai Belanja Online yang Aman',
                'isi' => "Belanja online memudahkan kita mendapatkan produk tanpa harus keluar rumah. Pastikan Anda bertransaksi di situs tepercaya, cek ulasan pembeli, dan gunakan metode pembayaran yang aman. Selalu periksa rincian pesanan sebelum melakukan pembayaran. Jika ada promo, pastikan syarat dan ketentuannya jelas."
            ],
            [
                'judul' => 'Tips Mengelola Ongkos Kirim agar Lebih Hemat',
                'isi' => "Biaya pengiriman sering kali menjadi pertimbangan utama. Gabungkan beberapa produk dalam satu pesanan, pilih kurir yang sesuai kebutuhan (cepat vs hemat), dan manfaatkan fitur estimasi ongkir di halaman checkout. Perhatikan juga momen free shipping dari toko."
            ],
            [
                'judul' => 'Cara Menulis Deskripsi Produk yang Menjual',
                'isi' => "Deskripsi produk yang baik harus singkat, jelas, dan fokus pada manfaat. Sertakan spesifikasi utama, bahan, ukuran, serta panduan perawatan bila perlu. Gunakan foto yang tajam dan konsisten dengan pencahayaan baik. Tampilkan testimoni pelanggan untuk meningkatkan kepercayaan."
            ],
            [
                'judul' => 'Mengenal Status Pengiriman: Dari Pickup hingga Delivered',
                'isi' => "Setiap paket melewati beberapa status: pickup, manifest, in transit, out for delivery, dan delivered. Pahami arti tiap status agar Anda bisa memperkirakan waktu tiba. Jika status tidak berubah dalam waktu lama, hubungi layanan pelanggan dengan menyertakan nomor resi."
            ],
            [
                'judul' => 'Strategi Promo Toko Online untuk Meningkatkan Penjualan',
                'isi' => "Gunakan kupon diskon bertahap (contoh: 10% untuk pelanggan baru), bundling produk, dan program loyalti. Jadwalkan kampanye pada momen khusus (payday, lebaran, akhir tahun) dan pantau metrik konversi agar promosi berikutnya lebih efektif."
            ],
        ];

        foreach ($items as $it) {
            $slugBase = Str::slug($it['judul']);
            $slug = $slugBase;
            $i = 1;
            while (Article::where('slug', $slug)->exists()) {
                $slug = $slugBase.'-'.$i;
                $i++;
            }

            Article::create([
                'judul' => $it['judul'],
                'slug' => $slug,
                'isi' => $it['isi'],
                'penulis_id' => $author->id,
                'diterbitkan_pada' => now(),
            ]);
        }
    }
}
