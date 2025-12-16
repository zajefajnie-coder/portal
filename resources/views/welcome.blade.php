<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h1 class="text-3xl font-bold mb-4">Witaj na Portalu Modelingowym</h1>
                    <p class="text-lg text-gray-600 mb-6">
                        Platforma społecznościowa dla twórców mody i fotografii. 
                        Prezentuj swoje portfolio, znajdź współpracę i uczestnicz w castingach.
                    </p>
                </div>
            </div>

            <!-- Najnowsze inspiracje -->
            <div class="mb-6">
                <h2 class="text-2xl font-bold mb-4">Najnowsze inspiracje społeczności</h2>
                @php
                    $latestPortfolios = \App\Models\Portfolio::with('user')->latest()->take(12)->get();
                @endphp
                
                @if($latestPortfolios->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($latestPortfolios as $portfolio)
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
                @else
                    <p class="text-gray-600">Brak portfolio do wyświetlenia. Bądź pierwszy!</p>
                @endif
            </div>

            <!-- Sekcje dla różnych grup -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-bold mb-2">Dla fotografów</h3>
                    <p class="text-gray-600 mb-4">Prezentuj swoje prace, znajdź modele i współpracuj z innymi twórcami.</p>
                    <a href="{{ route('portfolios.index') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">Zobacz portfolia →</a>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-bold mb-2">Dla modelek i modeli</h3>
                    <p class="text-gray-600 mb-4">Stwórz profesjonalne portfolio i znajdź castingi dopasowane do Ciebie.</p>
                    <a href="{{ route('castings.index') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">Zobacz castingi →</a>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-bold mb-2">Zespół kreatywny</h3>
                    <p class="text-gray-600 mb-4">Wizażyści, stylści, fryzjerzy - dołącz do społeczności profesjonalistów.</p>
                    <a href="{{ route('register') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">Dołącz teraz →</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


