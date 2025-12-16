<x-app-layout>
    <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <img src="{{ $portfolio->image_url }}" alt="{{ $portfolio->title }}" class="w-full h-auto">
                    <div class="p-6">
                        <h1 class="text-3xl font-bold mb-2">{{ $portfolio->title ?: 'Bez tytułu' }}</h1>
                        <div class="flex items-center mb-4">
                            <span class="text-gray-600">Autor: </span>
                            <a href="#" class="ml-2 text-indigo-600 hover:text-indigo-800 font-semibold">{{ $portfolio->user->name }}</a>
                        </div>
                        @if($portfolio->description)
                            <p class="text-gray-700 mb-4">{{ $portfolio->description }}</p>
                        @endif
                        <p class="text-sm text-gray-500">{{ $portfolio->created_at->format('d.m.Y H:i') }}</p>

                        @auth
                            @if(auth()->id() === $portfolio->user_id)
                                <div class="mt-6 flex gap-4">
                                    <a href="{{ route('portfolios.edit', $portfolio) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                                        Edytuj
                                    </a>
                                    <form action="{{ route('portfolios.destroy', $portfolio) }}" method="POST" onsubmit="return confirm('Czy na pewno chcesz usunąć to portfolio?');">
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
        </div>
</x-app-layout>


