<x-app-layout>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-6">Edytuj Portfolio</h1>

                    <form action="{{ route('portfolios.update', $portfolio) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <x-input-label for="title" value="Tytuł (opcjonalnie)" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $portfolio->title)" />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="description" value="Opis (opcjonalnie)" />
                            <textarea id="description" name="description" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description', $portfolio->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="image" value="Nowe zdjęcie (opcjonalnie)" />
                            <input id="image" type="file" name="image" accept="image/*" class="block mt-1 w-full">
                            <p class="mt-1 text-sm text-gray-500">Obecne zdjęcie:</p>
                            <img src="{{ $portfolio->image_url }}" alt="{{ $portfolio->title }}" class="mt-2 w-32 h-32 object-cover rounded">
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end">
                            <a href="{{ route('portfolios.show', $portfolio) }}" class="text-gray-600 hover:text-gray-900 mr-4">Anuluj</a>
                            <x-primary-button>Zaktualizuj Portfolio</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


