@php($appName = config('app.name', 'Aplikasi'))
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f6f6f6; padding:20px;">
    <div style="max-width:600px; margin:0 auto; background:#ffffff; border-radius:8px; padding:24px;">
        <h2 style="margin-top:0; color:#111827;">Verifikasi Email Anda</h2>
        <p>Terima kasih telah mendaftar di {{ $appName }}. Klik tombol di bawah untuk memverifikasi email dan melanjutkan pengisian data pendaftaran Anda.</p>
        <p style="margin: 24px 0;">
            <a href="{{ $url }}" style="display:inline-block; background:#2563eb; color:#ffffff; padding:12px 20px; text-decoration:none; border-radius:6px;">Verifikasi Email</a>
        </p>
        <p>Atau salin dan tempel tautan berikut ke peramban Anda:</p>
        <p><a href="{{ $url }}">{{ $url }}</a></p>
        <p style="color:#6b7280; font-size:12px;">Tautan ini akan kedaluwarsa dalam 60 menit.</p>
    </div>
</body>
</html>
