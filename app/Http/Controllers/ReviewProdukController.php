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
            $data = $request->validate([
                'rating' => ['required','integer','min:1','max:5'],
                'review' => ['nullable','string','max:5000'],
            ]);

            // Satu ulasan per user per produk: upsert jika sudah ada
            $existing = ReviewProduk::where('produk_id', $produk->produk_id)
                ->where('pengguna_id', $user->id)
                ->first();

            if ($existing) {
                $existing->update([
                    'rating' => $data['rating'],
                    'review' => $data['review'] ?? $existing->review,
                ]);
                $review = $existing->fresh('pengguna');
            } else {
                $review = ReviewProduk::create([
                    'review_id' => (string) Str::uuid(),
                    'produk_id' => $produk->produk_id,
                    'pengguna_id' => $user->id,
                    'rating' => $data['rating'],
                    'review' => $data['review'] ?? null,
                ]);
            }

            $produk->recalcRating();
            if($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Ulasan tersimpan.',
                    'data' => [
                        'rating_avg' => (float) $produk->rating_avg,
                        'rating_count' => (int) $produk->rating_count,
                        'review' => $review,
                    ]
                ], $existing ? 200 : 201);
            }
            return back()->with('success', 'Terima kasih atas ulasan Anda.');
        } catch (\Throwable $e) {
            if($request->expectsJson()) {
                return response()->json(['message' => $this->errorMessage($e, 'Gagal menyimpan ulasan.')], 500);
            }
            $this->logException($e, ['action' => 'ReviewProdukController@store', 'produk_id' => $produk->produk_id ?? null]);
            return back()->withInput()->with('error', $this->errorMessage($e, 'Gagal menyimpan ulasan.'));
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

    /**
     * Display the specified resource.
     */
    public function show(ReviewProduk $reviewProduk)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ReviewProduk $reviewProduk)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
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
