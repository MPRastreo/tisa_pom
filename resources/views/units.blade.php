<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Unidades') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-5">
            @if ($errors->any())
                <div class="bg-red-200 border border-transparent text-red-700 px-4 py-3 mb-5 rounded relative"
                    role="alert">
                    <strong class="font-bold">¡Lo sentimos!</strong>
                    <span class="block sm:inline">{{ $errors->first() }}</span>
                </div>
            @endif
            @if (session('success'))
                <div class="bg-green-200 border border-transparent text-green-700 px-4 py-3 mb-5 rounded relative"
                    role="alert">
                    <strong class="font-bold">¡Éxito!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <x-text-input id="searchInput" class="w-full px-4 py-2 mb-4 border border-gray-300 rounded-md focus:outline-none focus:ring "  placeholder="Buscar por nombre de unidad" autofocus />

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4" id="unitContainer">
                @foreach ($units as $unit)
                    <div class="flex flex-col rounded-md bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 bg-clip-border text-gray-700 shadow-2xl unit-card">
                        <div class="p-6">
                            <div class="flex space-x-3 pt-1" aria-current="true">
                                <div
                                    class="inline-flex items-center justify-center rounded-full flex-shrink-0 h-16 w-16">
                                    <div class="flex items-center justify-center w-16 h-16 rounded-full dark:bg-gray-50">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 dark:text-gray-800"
                                            aria-hidden="true" fill="currentColor" viewBox="0 0 640 512">
                                            <path
                                                d="M48 0C21.5 0 0 21.5 0 48V368c0 26.5 21.5 48 48 48H64c0 53 43 96 96 96s96-43 96-96H384c0 53 43 96 96 96s96-43 96-96h32c17.7 0 32-14.3 32-32s-14.3-32-32-32V288 256 237.3c0-17-6.7-33.3-18.7-45.3L512 114.7c-12-12-28.3-18.7-45.3-18.7H416V48c0-26.5-21.5-48-48-48H48zM416 160h50.7L544 237.3V256H416V160zM112 416a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm368-48a48 48 0 1 1 0 96 48 48 0 1 1 0-96z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex ml-4 space-x-2 w-full justify-between items-center">
                                    <div>
                                        @if ($unit->status_events)
                                            <span
                                                class="inline-flex items-center rounded-lg bg-green-200 px-2 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Eventos
                                                activados</span>
                                        @else
                                            <span
                                                class="inline-flex items-center rounded-lg bg-gray-100 px-2 text-xs font-medium text-gray-700 ring-1 ring-inset ring-gray-600/20">Eventos
                                                desactivados</span>
                                        @endif
                                        <h5
                                            class="block text-gray-50 font-sans text-xl font-semibold leading-snug tracking-normal text-blue-gray-900 antialiased">
                                            Unidad {{ $unit->unit_name }}</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="flex justify-center">
                                    <a href="{{ route('events.toggle', ['id' => $unit->id]) }}"
                                        onclick="event.preventDefault(); document.getElementById('toggle-form-{{ $unit->id }}').submit();">
                                        <x-primary-button type="button">
                                            {{ __($unit->status_events ? 'Desactivar' : 'Activar') }}
                                        </x-primary-button>
                                    </a>
                                    <form id="toggle-form-{{ $unit->id }}"
                                        action="{{ route('events.toggle', ['id' => $unit->id]) }}" method="POST"
                                        style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <script>
        const searchInput = document.getElementById('searchInput');
        const unitCards = document.getElementsByClassName('unit-card');

        searchInput.addEventListener('input', function ()
        {
            const searchValue = this.value.toLowerCase();

            for (let i = 0; i < unitCards.length; i++)
            {
                const unitName = unitCards[i].querySelector('h5').textContent.toLowerCase();
                (unitName.includes(searchValue)) ? unitCards[i].style.display = 'block': unitCards[i].style.display = 'none';
            }
        });
    </script>
</x-app-layout>
