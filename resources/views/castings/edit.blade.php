<x-app-layout>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-6">Edytuj Casting</h1>

                    <form action="{{ route('castings.update', $casting) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <x-input-label for="title" value="Tytuł *" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $casting->title)" required />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="description" value="Opis (opcjonalnie)" />
                            <textarea id="description" name="description" rows="6" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description', $casting->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="location" value="Lokalizacja (opcjonalnie)" />
                            <x-text-input id="location" class="block mt-1 w-full" type="text" name="location" :value="old('location', $casting->location)" />
                            <x-input-error :messages="$errors->get('location')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="casting_date" value="Data castingu (opcjonalnie)" />
                            <x-text-input id="casting_date" class="block mt-1 w-full" type="date" name="casting_date" :value="old('casting_date', $casting->casting_date?->format('Y-m-d'))" />
                            <x-input-error :messages="$errors->get('casting_date')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="required_roles" value="Wymagane role *" />
                            <div class="mt-2 space-y-2">
                                @php
                                    $roles = ['photographer' => 'Fotograf', 'model' => 'Model/Modelka', 'makeup_artist' => 'Wizażysta', 'stylist' => 'Stylista', 'hairdresser' => 'Fryzjer', 'retoucher' => 'Retuszer'];
                                    $selectedRoles = old('required_roles', $casting->required_roles ?? []);
                                @endphp
                                @foreach($roles as $key => $label)
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="required_roles[]" value="{{ $key }}" 
                                               {{ in_array($key, $selectedRoles) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-600">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('required_roles')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="status" value="Status *" />
                            <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="open" {{ old('status', $casting->status) === 'open' ? 'selected' : '' }}>Otwarty</option>
                                <option value="closed" {{ old('status', $casting->status) === 'closed' ? 'selected' : '' }}>Zamknięty</option>
                                <option value="filled" {{ old('status', $casting->status) === 'filled' ? 'selected' : '' }}>Zapełniony</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end">
                            <a href="{{ route('castings.show', $casting) }}" class="text-gray-600 hover:text-gray-900 mr-4">Anuluj</a>
                            <x-primary-button>Zaktualizuj Casting</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


