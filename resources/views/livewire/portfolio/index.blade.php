<x-app-layout>
    <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-6 flex justify-between items-center">
                    <h1 class="text-3xl font-bold">Portfolia</h1>
                    @auth
                        <a href="{{ route('portfolios.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                            Dodaj Portfolio
                        </a>
                    @endauth
                </div>

                <!-- Search -->
                <div class="mb-6">
                    <input type="text" wire:model.live.debounce.300ms="search" 
                           placeholder="Szukaj portfolio..." 
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                @if($portfolios->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($portfolios as $portfolio)
                            <a href="{{ route('portfolios.show', $portfolio) }}" class="group">
                                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                                    <img src="{{ $portfolio->image_url }}" alt="{{ $portfolio->title }}" class="w-full h-64 object-cover">
                                    <div class="p-4">
                                        <h3 class="font-semibold text-gray-900 group-hover:text-indigo-600">{{ $portfolio->title ?: 'Bez tytułu' }}</h3>
                                        <p class="text-sm text-gray-600 mt-1">{{ $portfolio->user->name }}</p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $portfolios->links() }}
                    </div>
                @else
                    <div class="bg-white rounded-lg shadow-md p-8 text-center">
                        <p class="text-gray-600 text-lg">Brak portfolio do wyświetlenia.</p>
                        @auth
                            <a href="{{ route('portfolios.create') }}" class="mt-4 inline-block bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                                Dodaj pierwsze portfolio
                            </a>
                        @endauth
                    </div>
                @endif
            </div>
        </div>
</x-app-layout>


