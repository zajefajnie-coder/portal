<x-app-layout>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-6">Utwórz Casting</h1>

                    <form action="{{ route('castings.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <x-input-label for="title" value="Tytuł *" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="description" value="Opis (opcjonalnie)" />
                            <textarea id="description" name="description" rows="6" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="location" value="Lokalizacja (opcjonalnie)" />
                            <x-text-input id="location" class="block mt-1 w-full" type="text" name="location" :value="old('location')" />
                            <x-input-error :messages="$errors->get('location')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="casting_date" value="Data castingu (opcjonalnie)" />
                            <x-text-input id="casting_date" class="block mt-1 w-full" type="date" name="casting_date" :value="old('casting_date')" />
                            <x-input-error :messages="$errors->get('casting_date')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="required_roles" value="Wymagane role *" />
                            <div class="mt-2 space-y-2">
                                @php
                                    $roles = ['photographer' => 'Fotograf', 'model' => 'Model/Modelka', 'makeup_artist' => 'Wizażysta', 'stylist' => 'Stylista', 'hairdresser' => 'Fryzjer', 'retoucher' => 'Retuszer'];
                                @endphp
                                @foreach($roles as $key => $label)
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="required_roles[]" value="{{ $key }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-600">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('required_roles')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end">
                            <a href="{{ route('castings.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Anuluj</a>
                            <x-primary-button>Utwórz Casting</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


