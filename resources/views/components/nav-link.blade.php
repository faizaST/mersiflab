@props(['active', 'icon' => ''])

@php
$classes = ($active ?? false)
            ? 'flex items-center gap-3 px-3 py-2.5 text-sky-600 bg-sky-50 rounded-lg shadow-sm border border-sky-100 transition-colors group'
            : 'flex items-center gap-3 px-3 py-2.5 text-slate-600 hover:text-sky-600 hover:bg-slate-50 rounded-lg transition-colors group';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)
        <i class="fa-solid {{ $icon }} w-6 text-center {{ ($active ?? false) ? 'text-sky-600' : 'text-slate-400 group-hover:text-sky-600' }} transition-colors"></i>
    @endif
    <span x-show="sidebarOpen" class="font-medium whitespace-nowrap">{{ $slot }}</span>
</a>
