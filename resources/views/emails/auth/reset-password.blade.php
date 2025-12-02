@component('mail::message')

<div style="text-align:center;margin-bottom:16px;">
  <img src="{{ asset('assets/images/logo.png') }}" alt="{{ $appName }}" height="40" style="height:40px;">
  <div style="font-size:14px;color:#94a3b8;margin-top:4px;">Permintaan Reset Password</div>
  <hr style="border:none;border-top:1px solid #e5e7eb;margin:12px 0;">
  <div style="font-weight:600;font-size:18px;color:#0ea5e9;">{{ $appName }}</div>
  <div style="font-size:12px;color:#64748b;">{{ $userEmail }}</div>
  <br>
</div>

Halo,

Kami menerima permintaan untuk mengatur ulang password akun kamu. Tekan tombol di bawah ini untuk melanjutkan.

@component('mail::button', ['url' => $url])
Atur Ulang Password
@endcomponent

Tautan ini berlaku selama {{ $expire }} menit. Jika kamu tidak merasa meminta reset password, abaikan email ini dan kata sandi kamu akan tetap aman.

Jika tombol di atas tidak berfungsi, salin dan tempel URL di bawah ini pada peramban kamu:

{{ $url }}

Terima kasih,
{{ $appName }}

@slot('subcopy')
Jika butuh bantuan, balas email ini atau hubungi kami di {{ $supportEmail }}.
@endslot

@endcomponent

