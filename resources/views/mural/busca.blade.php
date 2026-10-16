<x-layouts::app :title="__('Busca no mural')">
    <div class="mx-auto flex h-full w-full max-w-2xl flex-col gap-6 p-6">
        <h1 class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">
            {{ __('Busca no mural') }}
        </h1>

        <form method="GET" action="{{ route('mural.busca') }}" class="flex flex-col gap-3 rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            <label for="q" class="text-sm font-medium text-zinc-700 dark:text-zinc-200">
                {{ __('Termo') }}
            </label>
            <input
                id="q"
                name="q"
                type="text"
                value="{{ $termo }}"
                placeholder="{{ __('Digite um trecho do recado...') }}"
                class="w-full rounded-md border border-zinc-300 bg-white p-2 text-sm text-zinc-900 focus:border-zinc-500 focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100"
            />
            <div class="flex justify-end">
                <button
                    type="submit"
                    class="rounded-md bg-zinc-900 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-700 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-300"
                >
                    {{ __('Buscar') }}
                </button>
            </div>
        </form>

        @if ($termo !== '')
            <section class="flex flex-col gap-3 rounded-xl border border-amber-300 bg-amber-50 p-4 text-sm dark:border-amber-700 dark:bg-amber-950/40">
                <div class="flex flex-col gap-1">
                    <h2 class="font-semibold text-amber-900 dark:text-amber-200">
                        {{ __('SQL (toSql)') }}
                    </h2>
                    <pre class="overflow-x-auto whitespace-pre-wrap break-words font-mono text-xs text-amber-900 dark:text-amber-100">{{ $sql }}</pre>
                </div>
                <div class="flex flex-col gap-1">
                    <h2 class="font-semibold text-amber-900 dark:text-amber-200">
                        {{ __('Bindings (getBindings)') }}
                    </h2>
                    <pre class="overflow-x-auto whitespace-pre-wrap break-words font-mono text-xs text-amber-900 dark:text-amber-100">{{ json_encode($bindings, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
                </div>
            </section>

            <ul class="flex flex-col gap-3">
                @forelse ($resultados as $recado)
                    <li class="flex flex-col gap-1 rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
                        <p class="whitespace-pre-line text-sm text-zinc-700 dark:text-zinc-200">
                            {{ $recado->texto }}
                        </p>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">
                            {{ $recado->created_at }}
                        </span>
                    </li>
                @empty
                    <li class="text-sm text-zinc-500 dark:text-zinc-400">
                        {{ __('Nenhum recado encontrado.') }}
                    </li>
                @endforelse
            </ul>
        @endif
    </div>
</x-layouts::app>