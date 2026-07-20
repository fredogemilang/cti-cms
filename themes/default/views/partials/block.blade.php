{{-- Block renderer — handles all CMS block types --}}
<div class="block block--{{ $block->type }}" data-block="{{ $block->name }}">
    @switch($block->type)

        @case('text')
            <p>{{ $block->localizedValue }}</p>
            @break

        @case('textarea')
            <div>{!! nl2br(e($block->localizedValue)) !!}</div>
            @break

        @case('wysiwyg')
            <div class="prose">{!! $block->localizedValue !!}</div>
            @break

        @case('number')
            <div class="block-number">
                <span class="block-number__value">
                    {{ $block->getOption('prefix') }}{{ $block->value }}{{ $block->getOption('suffix') }}
                </span>
                @if($block->label)
                    <span class="block-number__label">{{ $block->label }}</span>
                @endif
            </div>
            @break

        @case('media')
            @if($block->value)
                <figure class="block-media">
                    <img src="{{ asset('storage/' . $block->value) }}"
                         alt="{{ $block->label }}"
                         loading="lazy">
                </figure>
            @endif
            @break

        @case('gallery')
            @php $images = $block->getDecodedValue() ?? []; @endphp
            @if(count($images))
                <div class="block-gallery">
                    @foreach($images as $image)
                        <figure class="block-gallery__item">
                            <img src="{{ asset('storage/' . $image) }}" alt="Gallery" loading="lazy">
                        </figure>
                    @endforeach
                </div>
            @endif
            @break

        @case('date')
            <p class="block-meta">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                {{ \Carbon\Carbon::parse($block->value)->format('F j, Y') }}
            </p>
            @break

        @case('datetime')
            <p class="block-meta">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                {{ \Carbon\Carbon::parse($block->value)->format('F j, Y \a\t g:i A') }}
            </p>
            @break

        @case('repeater')
            @php
                $rows = $block->localizedValue();
                if (!is_array($rows)) $rows = [];
            @endphp
            @if(count($rows))
                <div class="block-grid">
                    @foreach($rows as $row)
                        <div class="card">
                            <div class="card-body">
                                @foreach($block->childBlocks as $child)
                                    @if($child->is_active && isset($row[$child->name]))
                                        @if($child->type === 'media' && $row[$child->name])
                                            <img src="{{ asset('storage/' . $row[$child->name]) }}"
                                                 alt="{{ $child->label }}"
                                                 class="card-image"
                                                 loading="lazy">
                                        @else
                                            <div class="card-field">
                                                <small class="card-label">{{ $child->label }}</small>
                                                <span>{{ $row[$child->name] }}</span>
                                            </div>
                                        @endif
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
            @break

        @case('select')
        @case('radio')
            <span>{{ $block->value }}</span>
            @break

        @case('checkbox')
            @php $values = $block->getDecodedValue() ?? []; @endphp
            <ul class="block-checklist">
                @foreach($values as $v)
                    <li>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-primary)" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        {{ $v }}
                    </li>
                @endforeach
            </ul>
            @break

        @case('switcher')
            @if($block->getDecodedValue())
                <span class="badge badge--success">Enabled</span>
            @else
                <span class="badge badge--muted">Disabled</span>
            @endif
            @break

        @case('color')
            <div class="block-color">
                <span class="block-color__swatch" style="background:{{ $block->value }}"></span>
                <code>{{ $block->value }}</code>
            </div>
            @break

        @default
            @if($block->value)
                <div class="block-default">{{ $block->value }}</div>
            @endif
    @endswitch
</div>
