<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Chat') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("¡Inicio de sesión exitoso!") }}
                </div>
            </div>
        </div>
    </div>
    <!-- SECCION DE API -->
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div id="datos">485</div>
                    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
                    <script>
                        axios.get('/datos')
                            .then(function (response) {
                                document.getElementById('datos').innerText = JSON.stringify(response.data);
                            })
                            .catch(function (error) {
                                console.error(error);
                            });
                    </script>
                </div>
            </div>
        </div>
    </div>
    <!-- FIN DE SECCION DE API -->
</x-app-layout>
