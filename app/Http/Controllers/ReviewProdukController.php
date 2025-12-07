<?php

namespace App\Http\Controllers;

use App\Models\ReviewProduk;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ReviewProdukController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth'])->only(['store','update','destroy']);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Produk $produk)
    {
        try {
            $user = $request->user();

            // Cek apakah user sudah pernah memberikan rating untuk produk ini
            $hasRated = ReviewProduk::where('produk_id', $produk->produk_id)
                ->where('pengguna_id', $user->id)
                ->whereNotNull('rating')
                ->exists();

            if (!$hasRated) {
                // Pertama kali: wajib isi rating, review opsional
                $data = $request->validate([
                    'rating' => ['required','integer','min:1','max:5'],
                    'review' => ['nullable','string','max:5000'],
                ]);

                $review = ReviewProduk::create([
                    'review_id' => (string) Str::uuid(),
                    'produk_id' => $produk->produk_id,
                    'pengguna_id' => $user->id,
                    'rating' => $data['rating'],
                    'review' => $data['review'] ?? null,
                ]);
            } else {
                // Sudah pernah memberi rating: tambah komentar baru tanpa mengubah rating (simpan ke tabel komentar)
                $data = $request->validate([
                    'review' => ['required','string','max:5000'],
                ]);
                $existing = ReviewProduk::where('produk_id', $produk->produk_id)
                    ->where('pengguna_id', $user->id)
                    ->whereNotNull('rating')
                    ->orderBy('created_at')
                    ->first();
                if (!$existing) {
                    // Safety fallback: jika entah bagaimana tidak ada rating, treat as first-time
                    $review = ReviewProduk::create([
                        'review_id' => (string) Str::uuid(),
                        'produk_id' => $produk->produk_id,
                        'pengguna_id' => $user->id,
                        'rating' => 5,
                        'review' => $data['review'],
                    ]);
                } else {
                    $review = \App\Models\ReviewKomentar::create([
                        'produk_id' => $produk->produk_id,
                        'review_id' => $existing->review_id,
                        'pengguna_id' => $user->id,
                        'komentar' => $data['review'],
                    ]);
                }
            }

            $produk->recalcRating();
            $review->load('pengguna');

            if($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => $hasRated ? 'Komentar ditambahkan.' : 'Ulasan tersimpan.',
                    'data' => [
                        'rating_avg' => (float) $produk->rating_avg,
                        'rating_count' => (int) $produk->rating_count,
                        'review' => $review,
                    ]
                ], $hasRated ? 201 : 201);
            }
            return back()->with('success', $hasRated ? 'Komentar ditambahkan.' : 'Terima kasih atas ulasan Anda.');
        } catch (\Throwable $e) {
            if($request->expectsJson()) {
                return response()->json(['message' => $this->errorMessage($e, 'Gagal menyimpan ulasan/komentar.')], 500);
            }
            $this->logException($e, ['action' => 'ReviewProdukController@store', 'produk_id' => $produk->produk_id ?? null]);
            return back()->withInput()->with('error', $this->errorMessage($e, 'Gagal menyimpan ulasan/komentar.'));
        }
    }

    /**
     * List reviews for a product (JSON, paginated).
     */
    public function indexByProduct(Request $request, Produk $produk)
    {
        $perPage = (int) min(max((int) $request->query('per_page', 10), 1), 50);
        $page = (int) max((int) $request->query('page', 1), 1);
        $query = $produk->reviews()->with('pengguna')->latest();

        if ($request->filled('user_id')) {
            $query->where('pengguna_id', $request->query('user_id'));
        }

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'status' => 'success',
            'data' => [
                'items' => $paginator->items(),
                'rating_avg' => (float) $produk->rating_avg,
                'rating_count' => (int) $produk->rating_count,
            ],
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    public function update(Request $request, ReviewProduk $review)
    {
        try {
            $user = $request->user();
            if ($review->pengguna_id !== $user->id) {
                abort(403);
            }
            $data = $request->validate([
                'rating' => ['required','integer','min:1','max:5'],
                'review' => ['nullable','string','max:5000'],
            ]);
            $review->update($data);
            $review->produk->recalcRating();
            if($request->expectsJson()) {
                $produk = $review->produk->fresh();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Ulasan diperbarui.',
                    'data' => [
                        'rating_avg' => (float) $produk->rating_avg,
                        'rating_count' => (int) $produk->rating_count,
                        'review' => $review->fresh('pengguna'),
                    ]
                ]);
            }
            return back()->with('success', 'Ulasan diperbarui.');
        } catch (\Throwable $e) {
            if($request->expectsJson()) {
                return response()->json(['message' => 'Gagal memperbarui ulasan.'], 500);
            }
            $this->logException($e, ['action' => 'ReviewProdukController@update', 'review_id' => $review->review_id ?? null]);
            return back()->withInput()->with('error', $this->errorMessage($e, 'Gagal memperbarui ulasan.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, ReviewProduk $review)
    {
        try {
            $user = $request->user();
            if ($review->pengguna_id !== $user->id) {
                abort(403);
            }
            $produk = $review->produk;
            $review->delete();
            $produk?->recalcRating();
            if($request->expectsJson()) {
                $produk = $produk?->fresh();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Ulasan dihapus.',
                    'data' => [
                        'rating_avg' => (float) ($produk->rating_avg ?? 0),
                        'rating_count' => (int) ($produk->rating_count ?? 0),
                        'deleted' => true,
                    ]
                ]);
            }
            return back()->with('success', 'Ulasan dihapus.');
        } catch (\Throwable $e) {
            if($request->expectsJson()) {
                return response()->json(['message' => 'Gagal menghapus ulasan.'], 500);
            }
            $this->logException($e, ['action' => 'ReviewProdukController@destroy', 'review_id' => $review->review_id ?? null]);
            return back()->with('error', $this->errorMessage($e, 'Gagal menghapus ulasan.'));
        }
    }
}
