@extends('layouts.app')

@section('title', 'Detail Pesanan - FishyGo')

@section('content')
<div class="max-w-5xl mx-auto py-8">
    <h1 class="text-3xl md:text-4xl font-bold text-primary mb-2">Detail Pesanan</h1>
    <p class="text-sm text-gray-500 mb-6">Kode: {{ $pesanan->kode_pesanan }}</p>

    <div class="grid md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="font-semibold">Status</div>
                        <div class="text-sm text-gray-600">{{ ucfirst(str_replace('_',' ', $pesanan->status)) }}</div>
                    </div>
                    <div class="text-right">
                        <div class="font-semibold">Total</div>
                        <div class="text-sm text-gray-600">Rp {{ number_format($pesanan->total, 0, ',', '.') }}</div>
                    </div>
                </div>
                @if(in_array($pesanan->status, ['menunggu_pembayaran','menunggu_konfirmasi']))
                    <form action="{{ route('pesanan.cancel', $pesanan->pesanan_id) }}" method="POST" class="mt-4 border-t pt-4">
                        @csrf
                        <div class="grid md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm mb-1">Alasan</label>
                                <select name="reason" class="select select-bordered w-full">
                                    <option value="">Pilih alasan</option>
                                    <option value="change_mind">Berubah pikiran</option>
                                    <option value="wrong_order">Salah pilih produk</option>
                                    <option value="payment_issue">Kendala pembayaran</option>
                                    <option value="other">Lainnya</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm mb-1">Catatan</label>
                                <input type="text" name="note" class="input input-bordered w-full" />
                            </div>
                        </div>
                        <button class="btn btn-error btn-sm text-white mt-3" onclick="return confirm('Batalkan pesanan ini?')">Batalkan Pesanan</button>
                    </form>
                @elseif($pesanan->status === 'dibatalkan')
                    <div class="mt-3 text-sm text-red-600">Dibatalkan pada {{ optional($pesanan->cancelled_at)->format('d M Y H:i') }}</div>
                @endif
            </div>

            <div class="bg-white rounded-xl shadow-sm p-4">
                <h2 class="font-semibold mb-3">Item Pesanan</h2>
                <div class="space-y-3">
                    @foreach($pesanan->items as $it)
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-medium">{{ $it->nama_produk_snapshot }}</div>
                                <div class="text-xs text-gray-500">Qty: {{ $it->qty }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm">Rp {{ number_format($it->harga_satuan, 0, ',', '.') }}</div>
                                <div class="text-xs text-gray-500">Subtotal: Rp {{ number_format($it->subtotal, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div>
            <div class="bg-white rounded-xl shadow-sm p-4">
                <h2 class="font-semibold mb-3">Pembayaran</h2>
                @php($pay = $pesanan->pembayaran->first())
                @if($pay)
                    <div class="text-sm">Status: <span class="font-medium">{{ ucfirst($pay->status) }}</span></div>
                    <div class="text-sm">Jumlah: Rp {{ number_format($pay->amount, 0, ',', '.') }}</div>
                    <div class="text-sm">Metode: {{ $pay->gateway }} - {{ $pay->channel }}</div>
                    <div class="text-xs text-gray-500 mt-2">Ref: {{ $pay->reference }}</div>
                    @php($manualBank = is_array($pay->gateway_payload ?? null) ? ($pay->gateway_payload['manual_bank'] ?? null) : null)
                    @php($rejectReason = is_array($pay->gateway_payload ?? null) ? ($pay->gateway_payload['manual_reject_reason'] ?? null) : null)
                    @if($pesanan->metode_pembayaran === 'manual' && $manualBank)
                        <div class="mt-2 text-sm">Bank: <span class="font-medium">{{ $manualBank }}</span></div>
                        <div class="mt-2 text-xs text-gray-600">
                            @php($bankInfo = [
                                'BCA' => 'Rekening BCA: 1234567890 a.n. PT FishyGo. Tambahkan berita: Kode Pesanan saat transfer.',
                                'BRI' => 'Rekening BRI: 9876543210 a.n. PT FishyGo. Pastikan unggah bukti setelah transfer.',
                                'BNI' => 'Rekening BNI: 1122334455 a.n. PT FishyGo. Simpan resi untuk validasi.',
                            ])
                            {{ $bankInfo[$manualBank] ?? '' }}
                        </div>
                    @endif
                    @if($pay->status === 'rejected' && $rejectReason)
                        <div class="alert alert-warning mt-3 text-sm">Bukti pembayaran ditolak: {{ $rejectReason }}</div>
                    @endif
                    @php($proofPath = is_array($pay->gateway_payload ?? null) ? ($pay->gateway_payload['manual_proof_path'] ?? null) : null)
                    @if($proofPath)
                        <div class="mt-3">
                            <div class="text-sm font-medium mb-1">Bukti Transfer</div>
                            <a href="{{ asset('storage/'.$proofPath) }}" target="_blank" class="inline-block">
                                <img src="{{ asset('storage/'.$proofPath) }}" alt="Bukti transfer" class="max-h-40 rounded border" />
                            </a>
                        </div>
                    @endif
                    @if($pay->status === 'paid')
                        <a class="btn btn-outline btn-sm w-full mt-3" href="{{ route('pesanan.receipt', $pesanan->pesanan_id) }}#print">Cetak Struk</a>
                    @endif
                @else
                    <div class="text-sm text-gray-500">Belum ada informasi pembayaran.</div>
                @endif

                @if(in_array($pesanan->status, ['menunggu_pembayaran','menunggu_konfirmasi']) && $pesanan->metode_pembayaran === 'midtrans')
                    <x-midtrans-snap-button
                        class="w-full mt-4"
                        :pesanan-id="$pesanan->pesanan_id"
                        :redirect="route('pesanan.show', $pesanan->pesanan_id)"
                    />
                @endif

                {{-- Manual Transfer: Upload bukti pembayaran (DaisyUI modal + preview) --}}
                @if(in_array($pesanan->status, ['menunggu_pembayaran','menunggu_konfirmasi']) && $pesanan->metode_pembayaran === 'manual')
                    <button class="btn btn-primary w-full mt-4" onclick="document.getElementById('modal-upload-bukti').showModal()">Upload Bukti Pembayaran</button>

                    <dialog id="modal-upload-bukti" class="modal">
                        <div class="modal-box max-w-md">
                            <h3 class="font-semibold text-lg mb-3">Upload Ulang Bukti Transfer</h3>
                            <form action="{{ route('pesanan.manual.upload', $pesanan->pesanan_id) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                                @csrf
                                <div>
                                    <input id="input-bukti" type="file" name="bukti" accept="image/*" class="file-input file-input-bordered w-full" required>
                                    <div class="text-xs text-gray-500 mt-2">Format gambar (JPG/PNG) maks 5MB.</div>
                                </div>
                                <div id="preview-wrap" class="hidden">
                                    <div class="text-sm font-medium mb-1">Preview</div>
                                    <img id="preview-img" src="" alt="Preview bukti" class="max-h-48 rounded border" />
                                </div>
                                <div class="modal-action">
                                    <button class="btn btn-primary">Kirim</button>
                                    <form method="dialog"><button class="btn">Batal</button></form>
                                </div>
                            </form>
                        </div>
                        <form method="dialog" class="modal-backdrop"><button>Tutup</button></form>
                    </dialog>

                    <script>
                        (function(){
                            const inp = document.getElementById('input-bukti');
                            const wrap = document.getElementById('preview-wrap');
                            const img = document.getElementById('preview-img');
                            if (inp) {
                                inp.addEventListener('change', function(){
                                    const f = this.files && this.files[0];
                                    if (!f) { wrap.classList.add('hidden'); img.src = ''; return; }
                                    if (!f.type.startsWith('image/')) { wrap.classList.add('hidden'); img.src = ''; return; }
                                    const reader = new FileReader();
                                    reader.onload = function(e){ img.src = e.target.result; wrap.classList.remove('hidden'); };
                                    reader.readAsDataURL(f);
                                });
                            }
                            // Auto open after checkout when no proof uploaded yet
                            @if(!$proofPath)
                                try { document.getElementById('modal-upload-bukti').showModal(); } catch (e) {}
                            @endif
                        })();
                    </script>
                @endif

                @if(in_array($pesanan->status, ['menunggu_pembayaran','menunggu_konfirmasi']) && $pesanan->metode_pembayaran === 'midtrans')
                    <div class="mt-4">
                        <x-midtrans-snap-button
                            :pesanan-id="$pesanan->pesanan_id"
                            label="Bayar Sekarang"
                            class="w-full"
                            :redirect="route('pesanan.show', $pesanan->pesanan_id)"
                        />
                    </div>
                    <script>
                        (function(){
                            const params = new URLSearchParams(window.location.search);
                            if (params.get('autopay') === '1') {
                                setTimeout(function(){
                                    const btn = document.querySelector('button#btn-pay-midtrans');
                                    if (btn && !btn.disabled) btn.click();
                                }, 200);
                            }
                        })();
                    </script>
                @endif

                @if(in_array($pesanan->status, ['menunggu_pembayaran','menunggu_konfirmasi']) && $pesanan->metode_pembayaran === 'cod')
                    @php($pickup = (bool) data_get($pesanan->alamat_snapshot, 'pickup', false))
                    <div class="alert alert-info mt-4">
                        @if($pickup)
                            Metode pembayaran COD (Ambil di tempat). Silakan lakukan pembayaran saat pengambilan.
                        @else
                            Metode pembayaran COD. Silakan siapkan pembayaran saat pesanan diantarkan.
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
