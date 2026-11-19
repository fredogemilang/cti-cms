@props(['paginator'])

@if(isset($paginator) && $paginator->hasPages())
    <div {{ $attributes->merge(['class' => 'mt-12 flex justify-center items-center gap-2']) }}>
        @php($paginationView = (active_theme()?->slug ?? 'default').'::partials.pagination')
        {{ view()->exists($paginationView) ? $paginator->links($paginationView) : $paginator->links() }}
    </div>
@endif
