<x-app-layout>
    <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-start mb-4">
                        <h1 class="text-3xl font-bold">{{ $casting->title }}</h1>
                        <span class="px-3 py-1 text-sm rounded
                            @if($casting->status === 'open') bg-green-100 text-green-800
                            @elseif($casting->status === 'closed') bg-red-100 text-red-800
                            @else bg-blue-100 text-blue-800
                            @endif">
                            @if($casting->status === 'open') Otwarty
                            @elseif($casting->status === 'closed') Zamknięty
                            @else Zapełniony
                            @endif
                        </span>
                    </div>

                    <div class="mb-4">
                        <span class="text-gray-600">Autor: </span>
                        <a href="#" class="text-indigo-600 hover:text-indigo-800 font-semibold">{{ $casting->user->name }}</a>
                    </div>

                    @if($casting->description)
                        <div class="mb-4">
                            <h2 class="text-xl font-semibold mb-2">Opis</h2>
                            <p class="text-gray-700 whitespace-pre-line">{{ $casting->description }}</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        @if($casting->location)
                            <div>
                                <span class="font-semibold">Lokalizacja:</span>
                                <p class="text-gray-700">{{ $casting->location }}</p>
                            </div>
                        @endif

                        @if($casting->casting_date)
                            <div>
                                <span class="font-semibold">Data castingu:</span>
                                <p class="text-gray-700">{{ $casting->casting_date->format('d.m.Y') }}</p>
                            </div>
                        @endif
                    </div>

                    @if($casting->required_roles)
                        <div class="mb-4">
                            <span class="font-semibold">Wymagane role:</span>
                            <div class="flex flex-wrap gap-2 mt-2">
                                @foreach($casting->required_roles as $role)
                                    <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded">{{ ucfirst(str_replace('_', ' ', $role)) }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <p class="text-sm text-gray-500 mb-6">Utworzono: {{ $casting->created_at->format('d.m.Y H:i') }}</p>

                    @auth
                        @if(auth()->id() === $casting->user_id)
                            <div class="flex gap-4">
                                <a href="{{ route('castings.edit', $casting) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                                    Edytuj
                                </a>
                                <form action="{{ route('castings.destroy', $casting) }}" method="POST" onsubmit="return confirm('Czy na pewno chcesz usunąć ten casting?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                                        Usuń
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
</x-app-layout>


