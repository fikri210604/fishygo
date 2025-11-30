@extends('layouts.admin')

@section('content')
<div class="mb-5 flex items-center justify-between">
    <div>
        <h1 class="text-xl font-semibold">Detail Pengiriman</h1>
        <div class="text-xs text-gray-500 mt-1">Kode Pesanan: {{ $pengiriman->pesanan->kode_pesanan }}</div>
    </div>
    <a href="{{ route('kurir.dashboard') }}" class="btn btn-sm">Kembali</a>
  </div>

  @if (session('error'))
      <div class="alert alert-error mb-4">{{ session('error') }}</div>
  @endif
  @if (session('success'))
      <div class="alert alert-success mb-4">{{ session('success') }}</div>
  @endif

  <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
      <table class="table">
          <tbody>
              <tr>
                  <th class="w-48">Nama Penerima</th>
                  <td>{{ $pengiriman->pesanan->alamat->penerima ?? '-' }}</td>
              </tr>
              <tr>
                  <th>Alamat</th>
                  <td class="text-sm">{{ $pengiriman->pesanan->alamat->alamat_lengkap ?? '-' }}</td>
              </tr>
              <tr>
                  <th>Telepon</th>
                  <td>{{ $pengiriman->pesanan->user->nomor_telepon ?? $pengiriman->pesanan->user->phone ?? '-' }}</td>
              </tr>
              <tr>
                  <th>Status</th>
                  <td>
                      <span class="badge {{ $pengiriman->status_class }}">{{ $pengiriman->status_label }}</span>
                  </td>
              </tr>
              <tr>
                  <th>Kurir Ditugaskan</th>
                  <td>{{ $pengiriman->kurir?->nama ?? $pengiriman->kurir?->email ?? '-' }}</td>
              </tr>
              <tr>
                  <th>Dikemas Pada</th>
                  <td>{{ optional($pengiriman->dikemas_pada)->format('d/m/Y H:i') ?? '-' }}</td>
              </tr>
              <tr>
                  <th>Dikirim Pada</th>
                  <td>{{ optional($pengiriman->dikirim_pada)->format('d/m/Y H:i') ?? '-' }}</td>
              </tr>
              <tr>
                  <th>Diterima Pada</th>
                  <td>{{ optional($pengiriman->diterima_pada)->format('d/m/Y H:i') ?? '-' }}</td>
              </tr>
          </tbody>
      </table>

      <div class="p-4 border-t">
          <div class="flex gap-2">
              @foreach ($pengiriman->action_buttons as $action => $label)
                  <form method="POST" action="{{ route('kurir.pengiriman.status', $pengiriman->pengiriman_id) }}">
                      @csrf
                      <input type="hidden" name="action" value="{{ $action }}">
                      <button class="{{ $pengiriman->button_class[$action] }}">{{ $label }}</button>
                  </form>
              @endforeach
          </div>
      </div>
  </div>
@endsection
