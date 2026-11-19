{{-- $items: ['Label' => url|null, ...] — "Dashboard" otomatis ditambahkan di awal --}}
<ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    @foreach ($items as $label => $url)
        @if ($url)
            <li class="breadcrumb-item"><a href="{{ $url }}">{{ $label }}</a></li>
        @else
            <li class="breadcrumb-item active" aria-current="page"><span>{{ $label }}</span></li>
        @endif
    @endforeach
</ol>
