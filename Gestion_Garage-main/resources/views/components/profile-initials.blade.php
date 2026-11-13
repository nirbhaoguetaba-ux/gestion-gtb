@props(['user', 'size' => 40])

@php
    $initials = mb_strtoupper(
        mb_substr($user->Prenom ?? '', 0, 1) . mb_substr($user->Nom ?? '', 0, 1)
    );
@endphp

<span
    class="profile-initials"
    style="--profile-initials-size: {{ $size }}px"
    role="img"
    aria-label="Initiales de {{ $user->full_name }}"
>{{ $initials ?: '·' }}</span>
