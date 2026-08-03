@props(['paginator'])

@if(isset($paginator) && $paginator->hasPages())
    <div {{ $attributes->merge(['class' => 'mt-12 flex justify-center items-center gap-2']) }}>
        {{ $paginator->links('cdt::partials.pagination') }}
    </div>
@endif
