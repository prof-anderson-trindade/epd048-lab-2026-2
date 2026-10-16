<x-layouts::app :title="__('Mural')">
    <div class="mx-auto flex h-full w-full max-w-2xl flex-col gap-6 p-6">
        <h1 class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">
            {{ __('Mural') }}
        </h1>

        <form method="POST" action="{{ route('mural.store') }}" class="flex flex-col gap-3 rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
            @csrf
            <textarea
                name="texto"
                rows="3"
                placeholder="{{ __('Escreva um recado...') }}"
                class="w-full resize-y rounded-md border border-zinc-300 bg-white p-2 text-sm text-zinc-900 focus:border-zinc-500 focus:outline-none dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-100"
            >{{ old('texto') }}</textarea>
            @error('texto')
                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
            <div class="flex justify-end">
                <button
                    type="submit"
                    class="rounded-md bg-zinc-900 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-700 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-300"
                >
                    {{ __('Enviar') }}
                </button>
            </div>
        </form>

        <ul class="flex flex-col gap-3">
            @forelse ($recados as $recado)
                <li class="flex flex-col gap-1 rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="flex items-center justify-between text-sm">
                        <span class="font-medium text-zinc-900 dark:text-zinc-100">
                            {{ $recado->user->name }}
                        </span>
                        <span class="text-zinc-500 dark:text-zinc-400">
                            {{ $recado->created_at->format('d/m/Y H:i') }}
                        </span>
                    </div>
                    <p class="whitespace-pre-line text-sm text-zinc-700 dark:text-zinc-200">
                        {{ $recado->texto }}
                    </p>
                </li>
            @empty
                <li class="text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('Nenhum recado ainda.') }}
                </li>
            @endforelse
        </ul>
    </div>
</x-layouts::app>
