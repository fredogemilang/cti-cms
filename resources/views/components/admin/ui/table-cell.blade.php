@props([
    'align' => 'left'
])

<td {{ $attributes->merge(['class' => 'px-4 py-5 align-middle text-sm text-gray-900 dark:text-[#FCFCFC]' . ($align === 'right' ? ' text-right' : '')]) }}>
    {{ $slot }}
</td>
