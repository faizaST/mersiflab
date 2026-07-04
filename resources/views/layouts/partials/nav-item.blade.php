<li>
    <a href="{{ $href }}"
       class="flex items-center gap-3 px-3 py-1.5 rounded-xl text-sm font-medium transition-all duration-150
              {{ $active
                  ? 'text-white font-semibold shadow-sm'
                  : 'text-slate-600 hover:bg-sky-50/70 hover:text-indigo-600' }}"
       style="{{ $active ? 'background: linear-gradient(135deg, #38bdf8 0%, #818cf8 100%); box-shadow: 0 4px 12px rgba(56,189,248,0.30);' : '' }}">
        <i class="fa-solid {{ $icon }} w-5 text-center text-base shrink-0
                  {{ $active ? 'text-white' : 'text-slate-400' }}"></i>
        <span x-show="sidebarOpen"
              x-transition:enter="transition ease-out duration-100"
              x-transition:enter-start="opacity-0"
              x-transition:enter-end="opacity-100"
              class="whitespace-nowrap">{{ $label }}</span>
        @if($active)
            <span x-show="sidebarOpen" class="ml-auto w-1.5 h-1.5 rounded-full bg-white/80 shrink-0"></span>
        @endif
    </a>
</li>
