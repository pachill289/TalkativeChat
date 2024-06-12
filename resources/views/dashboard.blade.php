<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Talkative Chat</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
</head>
<style>

        #chart-container {
            width: 85%;
            max-width: 700;
            background: white;
            padding: 25px;
            margin-top: 20px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);

        }        
        
        .div_dashboard{
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center; /* Centra el texto internamente */
        }
        
</style>
</head>

<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200">
  <x-app-layout>
   
    <!-- Dashboard class  -->
    <div class="div_dashboard">
    <div id="chart-container">
        <canvas id="messagesChart"></canvas>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        async function fetchMessages() {
            try {
                const response = await fetch('http://localhost:3000/getMessages');
                const messages = await response.json();
                return messages;
            } catch (error) {
                console.error('Error fetching messages:', error);
                return [];
            }
        }

        function processData(messages) {
            const dailyCount = {};
            messages.forEach(message => {
                const date = new Date(message.created_at).toISOString().split('T')[0];
                dailyCount[date] = (dailyCount[date] || 0) + 1;
            });
            const labels = Object.keys(dailyCount).sort();
            const data = labels.map(date => dailyCount[date]);
            return { labels, data };
        }

        async function createChart() {
            const messages = await fetchMessages();
            const { labels, data } = processData(messages);

            const ctx = document.getElementById('messagesChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Mensajes por Día',
                        data: data,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        fill: true,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        }
                    },
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Fecha'
                            }
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Cantidad de Mensajes'
                            },
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        createChart();
    </script>
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
