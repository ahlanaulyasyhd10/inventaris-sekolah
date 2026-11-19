{{-- $user (boleh null), $size: xs|sm|md|lg|xl (bawaan sm) --}}
@php($size = $size ?? 'sm')
@if ($user && $user->photoUrl())
    <span class="avatar avatar-{{ $size }}" style="background-image: url({{ $user->photoUrl() }})"></span>
@else
    <span class="avatar avatar-{{ $size }} bg-primary-lt">{{ strtoupper(mb_substr($user->name ?? '?', 0, 1)) }}</span>
@endif
