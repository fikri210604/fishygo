@props([
    'modalId' => null,
    'title' => 'Konfirmasi',
    'message' => 'Apakah Anda yakin?',
    'confirmText' => 'Ya',
    'cancelText' => 'Batal',
    'variant' => 'warning',
    'href' => null,      // navigate to URL on confirm
    'form' => null,      // submit external form by id on confirm
    'action' => null,    // submit internal form action on confirm
    'method' => 'POST',  // method for internal form
    'showTrigger' => false,
    'triggerText' => null,
    'triggerClass' => 'btn btn-error btn-xs text-white',
])

@php
    if (!$modalId) { $modalId = 'confirm-'.\Illuminate\Support\Str::random(8); }
    $btnClass = match($variant) {
        'danger' => 'btn-error',
        'success' => 'btn-success',
        'info' => 'btn-info',
        'primary' => 'btn-primary',
        default => 'btn-warning',
    };
    $http = strtoupper($method ?? 'POST');
    $simpleMethod = in_array($http, ['GET','POST']) ? $http : 'POST';
@endphp

@if($showTrigger)
    <button type="button" onclick="document.getElementById('{{ $modalId }}').showModal()" class="{{ $triggerClass }}">{{ $triggerText ?? 'Konfirmasi' }}</button>
@elseif(trim($slot ?? '') !== '')
    <span role="button" tabindex="0" onclick="document.getElementById('{{ $modalId }}').showModal()">{{ $slot }}</span>
@endif

<dialog id="{{ $modalId }}" class="modal">
  <div class="modal-box duration-100">
    <h3 id="{{ $modalId }}-title" class="font-bold text-lg">{{ $title }}</h3>
    <p class="py-4 text-sm">{{ $message }}</p>
    <div class="modal-action">
      <form method="dialog"><button class="btn">{{ $cancelText }}</button></form>

      @if($href)
        <a href="{{ $href }}" class="btn text-white {{ $btnClass }}">{{ $confirmText }}</a>
      @elseif($form)
        <button type="submit" form="{{ $form }}" class="btn text-white {{ $btnClass }}">{{ $confirmText }}</button>
      @elseif($action)
        <form method="{{ $simpleMethod }}" action="{{ $action }}">
          @csrf
          @if(!in_array($http, ['GET','POST']))
            @method($http)
          @endif
          <button type="submit" class="btn text-white {{ $btnClass }}">{{ $confirmText }}</button>
        </form>
      @endif
    </div>
  </div>
  <form method="dialog" class="modal-backdrop"><button>Tutup</button></form>
</dialog>
