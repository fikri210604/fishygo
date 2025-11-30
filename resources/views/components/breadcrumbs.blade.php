@php
    use Illuminate\Support\Str;

    $segments = request()->segments();
    $breadcrumbs = [
        ['label' => 'Home', 'url' => url('/')],
    ];

    $path = '';
    foreach ($segments as $i => $seg) {
        $path .= '/' . $seg;
        $label = Str::title(str_replace(['-', '_'], ' ', $seg));
        if ($i < count($segments) - 1) {
            $breadcrumbs[] = ['label' => $label, 'url' => url($path)];
        } 
        else {
            $breadcrumbs[] = ['label' => $label];
        }
    }
@endphp

@if(count($breadcrumbs))
<div class="breadcrumbs text-sm">
    <ul>
        @foreach ($breadcrumbs as $i => $item)
            @if($i > 0)
                <li class="opacity-60">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" 
                        fill="none" stroke="currentColor" stroke-width="2"
                        class="w-3.5 h-3.5">
                        <path d="M7 5l5 5-5 5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </li>
            @endif

            @if(!empty($item['url']))
                <li><a href="{{ $item['url'] }}">{{ $item['label'] }}</a></li>
            @else
                <li class="font-semibold text-blue-600">{{ $item['label'] }}</li>
            @endif
        @endforeach
    </ul>
</div>
@endif
