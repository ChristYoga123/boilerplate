@php
    $href = $href ?? 'javascript:void(0)';
    $title = $title ?? 'Edit';
    $permission = $permission ?? null;
    if (!isset($attributes) || !($attributes instanceof \Illuminate\View\ComponentAttributeBag)) {
        $attributes = new \Illuminate\View\ComponentAttributeBag([]);
    }
@endphp

@if(!$permission || auth()->user()?->can($permission))
<a href="{{ $href }}" {{ $attributes->merge(['class' => 'avatar-text avatar-md', 'title' => $title]) }}>
    <i class="feather feather-edit-3"></i>
</a>
@endif
