<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Talkative Chat</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200">
  <x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Página de bienvenida') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 bg-green-600 dark:text-gray-100">
                    {{ __("¡Inicio de sesión exitoso!") }}
                </div>
            </div>
        </div>
    </div>
    
    <!-- SECCION INTRODUCCIÓN -->
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 text-lg dark:text-gray-100">
                    <div class="mb-6">
                        <h3 class="text-3xl font-bold mb-4">¡Bienvenido a Talkative Chat!</h3>
                        <p>
                            Estamos encantados de tenerte con nosotros. Nuestro objetivo es ofrecer una experiencia de comunicación intuitiva y eficiente, ayudándote a mantenerte conectado con tus amigos, familiares y conocidos de una manera más fácil y divertida 😃.
                        </p>
                    </div>
                    <div class="swiper-container mb-6">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide bg-blue-600 text-white p-4 rounded shadow-lg"><b>Enviar Mensajes: </b>
                                <br>
                                Comunicarse nunca ha sido tan sencillo. Envía mensajes de texto rápidos y confiables a cualquier usuario en nuestra plataforma.
                            </div>
                            <div class="swiper-slide bg-green-600 text-white p-4 rounded shadow-lg"><b>Videollamadas: </b>
                                <br>
                                ¿Extrañan ver a sus seres queridos o necesitan una reunión cara a cara con su equipo? Nuestra función de videollamadas les permite conectarse con alta calidad de video y audio.</div>
                            <div class="swiper-slide bg-red-600 text-white p-4 rounded shadow-lg"><b> Formar Grupos: </b>
                                <br>
                                Ya sea para un proyecto de trabajo, un grupo de estudio o simplemente para mantenerse en contacto con un grupo de amigos, pueden crear grupos fácilmente y gestionar todas sus conversaciones en un solo lugar.</div>
                            <div class="swiper-slide bg-yellow-600 text-white p-4 rounded shadow-lg"><b> Muchas Más Acciones Útiles: </b>
                                <br>
                                Desde compartir archivos importantes hasta enviar emojis y stickers para darle vida a sus chats, nuestro sistema de mensajería está repleto de funcionalidades diseñadas para mejorar la comunicación diaria.</div>
                        </div>
                        <!-- Add Pagination -->
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SECCION DE API -->
    {{-- <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
                    <script>
                        axios.get('/datos')
                            .then(function (response) {
                                document.getElementById('datos').innerText = JSON.stringify(response.data);
                            })
                            .catch(function (error) {
                                console.error(error);
                            });
                    </script> --}}
  </x-app-layout>

  <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
  <script>
    var swiper = new Swiper('.swiper-container', {
      slidesPerView: 1,
      spaceBetween: 10,
      pagination: {
        el: '.swiper-pagination',
        clickable: true,
      },
      autoplay: {
        delay: 3000, // tiempo de espera en milisegundos entre transiciones
        disableOnInteraction: false, // si se debe detener al interactuar con él
      }
    });
  </script>
</body>
</html>
