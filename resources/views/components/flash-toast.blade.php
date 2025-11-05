@php
    $messages = [];
    if (session('success') || session('status')) {
        $messages[] = ['type' => 'success', 'title' => 'Berhasil', 'text' => session('success') ?? session('status')];
    }
    if (session('error')) {
        $messages[] = ['type' => 'error', 'title' => 'Terjadi Kesalahan', 'text' => session('error')];
    }
    if (session('warning')) {
        $messages[] = ['type' => 'warning', 'title' => 'Perhatian', 'text' => session('warning')];
    }
    if (session('info')) {
        $messages[] = ['type' => 'info', 'title' => 'Informasi', 'text' => session('info')];
    }
    if (($errors ?? null)?->any()) {
        $messages[] = ['type' => 'error', 'title' => 'Validasi Gagal', 'text' => ($errors ?? collect())->first()];
    }
@endphp

@if (count($messages))
<div class="toast toast-top toast-end z-[60]" aria-live="polite" aria-atomic="true">
    @foreach ($messages as $m)
        @php
            $type = $m['type'];
            $alertClass = match ($type) {
                'success' => 'alert-success',
                'warning' => 'alert-warning',
                'error' => 'alert-error',
                default => 'alert-info',
            };
        @endphp

        <div
            x-data="{ show: true }"
            x-show="show"
            x-transition.opacity.duration.300ms
            x-transition.scale.duration.300ms
            x-init="setTimeout(() => show = false, 3500)"
            role="alert"
            class="alert {{ $alertClass }} shadow-lg w-80 sm:w-96 mb-2 animate-fade-in-down"
        >
            <div class="flex items-start gap-3 w-full">
                {{-- Icon sesuai tipe alert --}}
                @switch($type)
                    @case('success')
                        <i class="fa-solid fa-circle-check text-2xl text-green-600"></i>
                        @break
                    @case('error')
                        <i class="fa-solid fa-circle-xmark text-2xl text-red-600"></i>
                        @break
                    @case('warning')
                        <i class="fa-solid fa-triangle-exclamation text-2xl text-yellow-600"></i>
                        @break
                    @default
                        <i class="fa-solid fa-circle-info text-2xl text-blue-600"></i>
                @endswitch

                {{-- Konten pesan --}}
                <div class="flex-1">
                    <div class="font-semibold text-base">{{ $m['title'] }}</div>
                    <div class="text-sm opacity-90">{{ $m['text'] }}</div>
                </div>

                {{-- Tombol tutup --}}
                <button
                    type="button"
                    class="btn btn-ghost btn-xs"
                    aria-label="Tutup"
                    @click="show = false"
                >
                    ✕
                </button>
            </div>
        </div>
    @endforeach
</div>
@endif
