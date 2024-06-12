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

        .contenedor{
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center; /* Centra el texto internamente */
            width:90%;
            margin-top: 20px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .container_users{
            display: flex;
            align-items: center;
            text-align: center; 
            margin: 150px;
            margin-top: 10px;
            margin-bottom: 1px;
        }
        
</style>
</head>

<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200">
  <x-app-layout>
   
    <!-- Dashboard class mensajes -->
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

    <!-- Dashboard class usuarios -->
    <div class="container_users">
        <div class="contenedor">
            <div id="chart-container">
                <canvas id="usersChart"></canvas>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                async function fetchUsers() {
                    try {
                        const response = await fetch('http://localhost:3000/users/view');
                        const users = await response.json();
                        return users;
                    } catch (error) {
                        console.error('Error fetching users:', error);
                        return [];
                    }
                }

                function processUserData(users) {
                    const dailyCount = {};
                    users.forEach(user => {
                        const date = new Date(user.created_at).toISOString().split('T')[0];
                        dailyCount[date] = (dailyCount[date] || 0) + 1;
                    });
                    const labels = Object.keys(dailyCount).sort();
                    const data = labels.map(date => dailyCount[date]);
                    return { labels, data };
                }

                async function createChart() {
                    const users = await fetchUsers();
                    const { labels, data } = processUserData(users);

                    const ctx = document.getElementById('usersChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Usuarios Registrados',
                                data: data,
                                backgroundColor: labels.map(() => getRandomColor()),
                                borderColor: 'rgba(0,0,0,0.1)',
                                borderWidth: 1,
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
                                        text: 'Cantidad de Usuarios'
                                    },
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }

                function getRandomColor() {
                    const letters = '0123456789ABCDEF';
                    let color = '#';
                    for (let i = 0; i < 6; i++) {
                        color += letters[Math.floor(Math.random() * 16)];
                    }
                    return color;
                }

                createChart();
            </script>
        </div>
        <div class="contenedor">
        <div id="chart-container">
        <canvas id="usersLineChart"></canvas>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        async function fetchUsers() {
            try {
                const response = await fetch('http://localhost:3000/users/view');
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                const users = await response.json();
                return users;
            } catch (error) {
                console.error('Error fetching users:', error);
                return [];
            }
        }

        function processUserData(users) {
            const dailyCount = {};
            users.forEach(user => {
                const date = new Date(user.created_at).toISOString().split('T')[0];
                dailyCount[date] = (dailyCount[date] || 0) + 1;
            });
            const labels = Object.keys(dailyCount).sort();
            const data = labels.map(date => dailyCount[date]);
            return { labels, data };
        }

        async function createLineChart() {
            const users = await fetchUsers();
            const { labels, data } = processUserData(users);

            if (!labels.length) {
                console.error('No data available to display.');
                return;
            }

            const ctx = document.getElementById('usersLineChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Usuarios Registrados',
                        data: data,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        fill: true,
                        tension: 0.4 // Añade suavidad a la línea
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
                                text: 'Cantidad de Usuarios'
                            },
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        createLineChart();
    </script>

        </div>
    </div>

    <div class="container_users">
        <div class="contenedor">
        <div id="chart-container">
                <canvas id="communitiesLineChart"></canvas>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                async function fetchCommunities() {
                    try {
                        const response = await fetch('http://localhost:3000/community/view');
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        const communities = await response.json();
                        return communities;
                    } catch (error) {
                        console.error('Error fetching communities:', error);
                        return [];
                    }
                }

                function processCommunityData(communities) {
                    const dailyCreations = {};
                    communities.forEach(community => {
                        const date = new Date(community.created_at).toISOString().split('T')[0];
                        dailyCreations[date] = (dailyCreations[date] || 0) + 1;
                    });
                    const labels = Object.keys(dailyCreations).sort();
                    const data = labels.map(date => dailyCreations[date]);
                    return { labels, data };
                }

                async function createLineChart() {
                    const communities = await fetchCommunities();
                    const { labels, data } = processCommunityData(communities);

                    if (!labels.length) {
                        console.error('No data available to display.');
                        return;
                    }

                    const ctx = document.getElementById('communitiesLineChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Comunidades Creadas',
                                data: data,
                                borderColor: 'rgba(255, 99, 132, 1)',
                                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                fill: true,
                                tension: 0.4 // Añade suavidad a la línea
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
                                        text: 'Cantidad de Comunidades'
                                    },
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }

                createLineChart();
            </script>
        </div>

        <div class="contenedor">
        <div id="chart-container">
                <canvas id="updatesLineChart"></canvas>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                async function fetchUsers() {
                    try {
                        const response = await fetch('http://localhost:3000/users/view');
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        const users = await response.json();
                        return users;
                    } catch (error) {
                        console.error('Error fetching users:', error);
                        return [];
                    }
                }

                function processUpdateData(users) {
                    const dailyUpdates = {};
                    users.forEach(user => {
                        const date = new Date(user.updated_at).toISOString().split('T')[0];
                        dailyUpdates[date] = (dailyUpdates[date] || 0) + 1;
                    });
                    const labels = Object.keys(dailyUpdates).sort();
                    const data = labels.map(date => dailyUpdates[date]);
                    return { labels, data };
                }

                async function createLineChart() {
                    const users = await fetchUsers();
                    const { labels, data } = processUpdateData(users);

                    if (!labels.length) {
                        console.error('No data available to display.');
                        return;
                    }

                    const ctx = document.getElementById('updatesLineChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Actualizaciones de Usuarios',
                                data: data,
                                borderColor: 'rgba(54, 162, 235, 1)',
                                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                fill: true,
                                tension: 0.4 // Añade suavidad a la línea
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
                                        text: 'Cantidad de Actualizaciones'
                                    },
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }

                createLineChart();
            </script>
        </div>
    </div>

    <!-- SECCION INTRODUCCIÓN -->
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 text-lg dark:text-gray-100">
                    <div class="mb-6">
                        <h3 class="text-3xl font-bold mb-4">¡Talkative Chat!</h3>
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
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
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
