{{-- resources/views/components/status-badge.blade.php --}}
<span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium {{ $colorClass }}">
    <span class="w-1.5 h-1.5 rounded-full bg-current opacity-70"></span>
    {{ $label }}
</span>