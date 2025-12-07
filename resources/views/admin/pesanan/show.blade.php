@extends('layouts.admin')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-semibold">Detail Pesanan</h1>
            <div class="text-xs text-gray-500 mt-1">Kode: {{ $pesanan->kode_pesanan }}</div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.pesanan.index') }}" class="btn btn-sm">Kembali</a>
            <form id="delete-pesanan-{{ $pesanan->pesanan_id }}" method="POST" action="{{ route('admin.pesanan.destroy', $pesanan->pesanan_id) }}">
                @csrf
                @method('DELETE')
            </form>
            <x-alert-confirmation
                :modal-id="'confirm-delete-pesanan-'.$pesanan->pesanan_id"
                title="Hapus Pesanan?"
                message="Tindakan ini tidak dapat dibatalkan."
                confirm-text="Hapus"
                cancel-text="Batal"
                variant="danger"
                :form="'delete-pesanan-'.$pesanan->pesanan_id"
            >
                <span class="btn btn-error btn-sm text-white">Hapus</span>
            </x-alert-confirmation>
        </div>
    </div>

    <div class="grid md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm p-4">
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <div class="text-xs text-gray-500">Status</div>
                        <div class="font-medium">{{ ucfirst(str_replace('_',' ', $pesanan->status)) }}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-gray-500">Total</div>
                        <div class="font-medium">Rp {{ number_format($pesanan->total, 0, ',', '.') }}</div>
                    </div>
                </div>
                <div class="mt-4 border-t pt-4">
                    @php($statusOptions = [
                        'menunggu_pembayaran' => 'Menunggu Pembayaran',
                        'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
                        'diproses' => 'Diproses',
                        'siap_diambil' => 'Siap Diambil',
                        'dikirim' => 'Dikirim',
                        'selesai' => 'Selesai',
                    ])
                    @if($pesanan->status === \App\Models\Pesanan::STATUS_DIBATALKAN)
                        <div class="text-xs text-red-600">Pesanan sudah dibatalkan, status tidak dapat diubah.</div>
                    @else
                        <form method="POST" action="{{ route('admin.pesanan.status.update', $pesanan->pesanan_id) }}" class="flex flex-wrap items-center gap-2">
                            @csrf
                            <label class="text-xs text-gray-500">Ubah Status</label>
                            <select name="status" class="select select-bordered select-sm select-no-truncate">
                                @foreach($statusOptions as $value => $label)
                                    <option value="{{ $value }}" {{ $pesanan->status === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <button class="btn btn-sm">Simpan</button>
                        </form>
                    @endif
                </div>
                @php($pay = $pesanan->pembayaran->first())
                @if($pesanan->metode_pembayaran === 'cod')
                    <div class="mt-4 flex items-center gap-2">
                        @if(!$pay || $pay->status !== 'paid')
                            <form id="cod-confirm-{{ $pesanan->pesanan_id }}" method="POST" action="{{ route('admin.pesanan.cod.confirm', $pesanan->pesanan_id) }}">
                                @csrf
                            </form>
                            <x-alert-confirmation
                                :modal-id="'confirm-cod-'.$pesanan->pesanan_id"
                                title="Konfirmasi Pembayaran COD?"
                                message="Pastikan pembayaran COD telah diterima."
                                confirm-text="Konfirmasi COD"
                                cancel-text="Batal"
                                variant="success"
                                :form="'cod-confirm-'.$pesanan->pesanan_id"
                            >
                                <span class="btn btn-primary btn-sm">Konfirmasi COD</span>
                            </x-alert-confirmation>
                            <button class="btn btn-error btn-sm text-white" onclick="document.getElementById('modal-cod-cancel').showModal()">Batalkan</button>
                        @else
                            <span class="badge badge-success">Lunas</span>
                        @endif
                        <a href="{{ route('admin.pesanan.receipt', $pesanan->pesanan_id) }}#print" class="btn btn-outline btn-sm">Cetak Struk</a>
                    </div>

                    <dialog id="modal-cod-cancel" class="modal">
                        <div class="modal-box max-w-md">
                            <h3 class="font-semibold text-lg mb-3">Batalkan Pesanan COD</h3>
                            <form method="POST" action="{{ route('admin.pesanan.cod.cancel', $pesanan->pesanan_id) }}" class="space-y-3">
                                @csrf
                                <div>
                                    <label class="label"><span class="label-text">Alasan</span></label>
                                    <select name="reason" class="select select-bordered w-full" required>
                                        <option value="cod_cancel">COD dibatalkan</option>
                                        <option value="user_request">Permintaan pelanggan</option>
                                        <option value="out_of_stock">Stok habis</option>
                                        <option value="other">Lainnya</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="label"><span class="label-text">Catatan</span></label>
                                    <textarea name="note" class="textarea textarea-bordered w-full" rows="3" placeholder="Opsional: catatan pembatalan"></textarea>
                                </div>
                                <div class="modal-action">
                                    <button class="btn btn-error text-white">Batalkan Pesanan</button>
                                    <form method="dialog"><button class="btn">Tutup</button></form>
                                </div>
                            </form>
                        </div>
                        <form method="dialog" class="modal-backdrop"><button>Tutup</button></form>
                    </dialog>
                @elseif($pesanan->metode_pembayaran === 'manual')
                    @php($proof = is_array($pay?->gateway_payload ?? null) ? ($pay->gateway_payload['manual_proof_path'] ?? null) : null)
                    <div class="mt-4 space-y-2">
                        <div class="text-sm">Transfer Manual</div>
                        @if($proof)
                            <a href="{{ asset('storage/'.$proof) }}" target="_blank" class="inline-block">
                                <img src="{{ asset('storage/'.$proof) }}" alt="Bukti transfer" class="max-h-40 rounded border" />
                            </a>
                        @else
                            <div class="text-xs text-gray-500">Belum ada bukti transfer yang diunggah.</div>
                        @endif
                        <div class="flex items-center gap-2">
                            @if(!$pay || $pay->status !== 'paid')
                                @if($proof)
                                    <form id="manual-confirm-{{ $pesanan->pesanan_id }}" method="POST" action="{{ route('admin.pesanan.manual.confirm', $pesanan->pesanan_id) }}">
                                        @csrf
                                    </form>
                                    <x-alert-confirmation
                                        :modal-id="'confirm-manual-'.$pesanan->pesanan_id"
                                        title="Konfirmasi Pembayaran Manual?"
                                        message="Pastikan bukti transfer valid."
                                        confirm-text="Konfirmasi"
                                        cancel-text="Batal"
                                        variant="success"
                                        :form="'manual-confirm-'.$pesanan->pesanan_id"
                                    >
                                        <span class="btn btn-primary btn-sm">Konfirmasi Pembayaran</span>
                                    </x-alert-confirmation>
                                @else
                                    <button class="btn btn-primary btn-sm" disabled>Konfirmasi Pembayaran</button>
                                @endif
                                <button class="btn btn-error btn-sm text-white" onclick="document.getElementById('modal-reject-manual').showModal()" {{ $proof ? '' : 'disabled' }}>Tolak</button>
                            @else
                                <span class="badge badge-success">Lunas</span>
                            @endif
                            <a href="{{ route('admin.pesanan.receipt', $pesanan->pesanan_id) }}#print" class="btn btn-outline btn-sm">Cetak Struk</a>
                        </div>
                        @php($rejectReason = is_array($pay?->gateway_payload ?? null) ? ($pay->gateway_payload['manual_reject_reason'] ?? null) : null)
                        @if($pay && $pay->status === 'rejected' && $rejectReason)
                            <div class="alert alert-warning text-xs">Ditolak: {{ $rejectReason }}</div>
                        @endif
                    </div>

                    <dialog id="modal-reject-manual" class="modal">
                        <div class="modal-box max-w-md">
                            <h3 class="font-semibold text-lg mb-3">Tolak Pembayaran Manual</h3>
                            <form method="POST" action="{{ route('admin.pesanan.manual.reject', $pesanan->pesanan_id) }}" class="space-y-3">
                                @csrf
                                <div>
                                    <label class="label"><span class="label-text">Alasan Penolakan</span></label>
                                    <textarea name="reason" class="textarea textarea-bordered w-full" rows="3" required placeholder="Contoh: bukti tidak jelas / nominal tidak sesuai"></textarea>
                                </div>
                                <div class="modal-action">
                                    <button class="btn btn-error text-white">Tolak</button>
                                    <form method="dialog"><button class="btn">Batal</button></form>
                                </div>
                            </form>
                        </div>
                        <form method="dialog" class="modal-backdrop"><button>Tutup</button></form>
                    </dialog>
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
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm p-4">
                <h2 class="font-semibold mb-2">Pelanggan</h2>
                <div class="text-sm">{{ $pesanan->user?->nama ?? '-' }}</div>
                <div class="text-xs text-gray-500">{{ $pesanan->user?->email }}</div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-4">
                <h2 class="font-semibold mb-2">Pembayaran</h2>
                @if($pay)
                    <div class="text-sm">Status: <span class="font-medium">{{ ucfirst($pay->status) }}</span></div>
                    <div class="text-sm">Jumlah: Rp {{ number_format($pay->amount, 0, ',', '.') }}</div>
                    <div class="text-sm">Metode: {{ $pay->gateway }} - {{ $pay->channel }}</div>
                    <div class="text-xs text-gray-500 mt-2">Ref: {{ $pay->reference }}</div>
                    @php($manualBank = is_array($pay->gateway_payload ?? null) ? ($pay->gateway_payload['manual_bank'] ?? null) : null)
                    @if($pesanan->metode_pembayaran === 'manual' && $manualBank)
                        <div class="mt-2 text-sm">Bank: <span class="font-medium">{{ $manualBank }}</span></div>
                    @endif
                    @if($pay->dibayar_pada)
                        <div class="text-xs text-gray-500 mt-1">Dibayar: {{ optional($pay->dibayar_pada)->format('d/m/Y H:i') }}</div>
                    @endif
                    @if($pay->paidBy)
                        <div class="text-xs text-gray-500 mt-1">Kasir: {{ $pay->paidBy->nama ?? $pay->paidBy->email }}</div>
                    @endif
                @else
                    <div class="text-sm text-gray-500">Belum ada informasi pembayaran.</div>
                @endif
            </div>

            <div class="bg-white rounded-xl shadow-sm p-4">
                <h2 class="font-semibold mb-2">Alamat</h2>
                @if($pesanan->alamat)
                    <div class="text-sm">{{ $pesanan->alamat->penerima }}</div>
                    <div class="text-sm">{{ $pesanan->alamat->alamat_lengkap }}</div>
                    <div class="text-xs text-gray-500 mt-1">{{ $pesanan->alamat->village_name }}, {{ $pesanan->alamat->district_name }}, {{ $pesanan->alamat->regency_name }}, {{ $pesanan->alamat->province_name }} {{ $pesanan->alamat->kode_pos }}</div>
                @else
                    <div class="text-sm text-gray-500">Alamat tidak tersedia.</div>
                @endif
            </div>
        </div>
    </div>
@endsection
