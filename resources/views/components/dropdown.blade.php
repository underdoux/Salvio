@props(['align' => 'right', 'width' => '48'])

<div x-data="{ open: false }" @click.away="open = false" @keydown.escape="open = false" class="relative">
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute z-50 mt-2 rounded-md shadow-lg origin-top-{{ $align }} ring-1 ring-black ring-opacity-5 focus:outline-none"
         style="display: none; min-width: calc({{ $width }} * 0.25rem);">
        <div class="rounded-md bg-white dark:bg-gray-800 shadow-xs">
            {{ $content }}
        </div>
    </div>
</div>
