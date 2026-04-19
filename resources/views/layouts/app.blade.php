<x-layouts::app.sidebar :title="$title ?? null">
    <flux:main>
        {{ $slot }}
    </flux:main>

    @persist('toasts')
        <flux:toast />
    @endpersist
</x-layouts::app.sidebar>
