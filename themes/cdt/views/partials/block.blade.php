{{-- CDT Theme Block Renderer --}}
@php
    $val = $block->localized_value;
@endphp

<div class="block-item block-{{ $block->type }} mb-8" data-block-name="{{ $block->name }}">
    @switch($block->type)
        @case('wysiwyg')
            <div class="prose max-w-none text-zinc-800 text-base md:text-lg leading-relaxed space-y-4 [&_a]:text-red-600 hover:[&_a]:text-red-700 [&_a]:underline [&_a]:font-medium transition-colors">
                {!! $val !!}
            </div>
            @break

        @case('textarea')
            <div class="text-zinc-800 text-base md:text-lg leading-relaxed whitespace-pre-line">
                {!! e($val) !!}
            </div>
            @break

        @case('text')
            <div class="text-zinc-900 text-lg font-medium">
                {{ $val }}
            </div>
            @break

        @case('media')
            @if($val)
                <div class="my-6 rounded-2xl overflow-hidden shadow-sm">
                    <img src="{{ resolve_block_asset($val) }}" alt="{{ $block->label ?? $block->name }}" class="w-full h-auto object-cover" loading="lazy">
                </div>
            @endif
            @break

        @case('gallery')
            @php $images = $block->getDecodedValue() ?? []; @endphp
            @if(count($images))
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 my-8">
                    @foreach($images as $img)
                        <div class="rounded-2xl overflow-hidden shadow-sm aspect-video bg-zinc-100">
                            <img src="{{ resolve_block_asset($img) }}" alt="Gallery Image" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300" loading="lazy">
                        </div>
                    @endforeach
                </div>
            @endif
            @break

        @case('repeater')
            @php $rows = is_array($val) ? $val : []; @endphp
            @if(count($rows))
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 my-8">
                    @foreach($rows as $row)
                        <div class="p-6 bg-zinc-50 border border-zinc-200 rounded-2xl">
                            @foreach($block->childBlocks as $child)
                                @if($child->is_active && isset($row[$child->name]))
                                    <div class="mb-3 last:mb-0">
                                        <div class="text-xs font-bold text-zinc-400 uppercase tracking-wider mb-1">{{ $child->label }}</div>
                                        <div class="text-zinc-800 text-sm">{!! is_array($row[$child->name]) ? json_encode($row[$child->name]) : e($row[$child->name]) !!}</div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @endif
            @break

        @default
            @if(!empty($val) && is_string($val))
                <div class="text-zinc-800 text-base">
                    {!! e($val) !!}
                </div>
            @endif
    @endswitch
</div>
