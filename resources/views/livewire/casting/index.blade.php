<x-app-layout>
    <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-6 flex justify-between items-center">
                    <h1 class="text-3xl font-bold">Castingi</h1>
                    @auth
                        <a href="{{ route('castings.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                            Utwórz Casting
                        </a>
                    @endauth
                </div>

                <!-- Filters -->
                <div class="mb-6 space-y-4">
                    <input type="text" wire:model.live.debounce.300ms="search" 
                           placeholder="Szukaj castingów..." 
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                    
                    <div class="flex gap-4">
                        <select wire:model.live="filterStatus" class="px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Wszystkie statusy</option>
                            <option value="open">Otwarte</option>
                            <option value="closed">Zamknięte</option>
                            <option value="filled">Zapełnione</option>
                        </select>

                        <select wire:model.live="filterRole" class="px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Wszystkie role</option>
                            <option value="photographer">Fotograf</option>
                            <option value="model">Model/Modelka</option>
                            <option value="makeup_artist">Wizażysta</option>
                            <option value="stylist">Stylista</option>
                            <option value="hairdresser">Fryzjer</option>
                            <option value="retoucher">Retuszer</option>
                        </select>
                    </div>
                </div>

                @if($castings->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($castings as $casting)
                            <a href="{{ route('castings.show', $casting) }}" class="group">
                                <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                                    <div class="flex justify-between items-start mb-2">
                                        <h3 class="text-xl font-bold text-gray-900 group-hover:text-indigo-600">{{ $casting->title }}</h3>
                                        <span class="px-2 py-1 text-xs rounded
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
                                    <p class="text-gray-600 mb-2">{{ Str::limit($casting->description, 100) }}</p>
                                    @if($casting->location)
                                        <p class="text-sm text-gray-500 mb-2">📍 {{ $casting->location }}</p>
                                    @endif
                                    @if($casting->casting_date)
                                        <p class="text-sm text-gray-500 mb-2">📅 {{ $casting->casting_date->format('d.m.Y') }}</p>
                                    @endif
                                    @if($casting->required_roles)
                                        <div class="flex flex-wrap gap-2 mt-2">
                                            @foreach($casting->required_roles as $role)
                                                <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">{{ ucfirst(str_replace('_', ' ', $role)) }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                    <p class="text-sm text-gray-500 mt-4">Autor: {{ $casting->user->name }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $castings->links() }}
                    </div>
                @else
                    <div class="bg-white rounded-lg shadow-md p-8 text-center">
                        <p class="text-gray-600 text-lg">Brak castingów do wyświetlenia.</p>
                        @auth
                            <a href="{{ route('castings.create') }}" class="mt-4 inline-block bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                                Utwórz pierwszy casting
                            </a>
                        @endauth
                    </div>
                @endif
            </div>
        </div>
</x-app-layout>


