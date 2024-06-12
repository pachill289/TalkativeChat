<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Comunidad</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900 text-gray-200">

    <div class="max-w-4xl mx-auto mt-10 p-6 bg-gray-800 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-6">Crear Comunidad</h1>
        
        <form method="GET" action="/community">
            <div class="mb-4">
                <label for="nombre" class="block text-sm font-medium text-gray-300">Nombre:</label>
                <input type="text" id="nombre" placeholder="Gamers, Aventureros, etc..." maxlength="30" class="mt-1 p-2 w-full bg-gray-700 border border-gray-600 rounded-md">
            </div>
            <div class="mb-4">
                <label for="max-integrantes" class="block text-sm font-medium text-gray-300">Número máximo de integrantes:</label>
                <input type="number" value="2" id="max-integrantes" placeholder="de 2 a 10" min="2" max="10" class="mt-1 p-2 w-full bg-gray-700 border border-gray-600 rounded-md">
            </div>
            <div class="mb-4">
                <label for="tipo" class="block text-sm font-medium text-gray-300">Tipo:</label>
                <input type="text" id="tipo" maxlength="25" placeholder="Aventuras, Cocina, Programación, etc..." class="mt-1 p-2 w-full bg-gray-700 border border-gray-600 rounded-md">
            </div>
            <div class="mb-4">
                <label for="query" class="block text-sm font-medium text-gray-300">Agregar usuarios:</label>
                <div class="flex">
                    <input type="text" id="query" name="query" placeholder="Nombre" value="{{ request('query') }}" class="mt-1 p-2 w-full bg-gray-700 border border-gray-600 rounded-md">
                    <button type="submit" class="ml-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-500">Buscar</button>
                </div>
            </div>
        </form>

        <table class="w-full text-left table-auto">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-gray-400">Usuario</th>
                    <th class="px-4 py-2 text-gray-400">Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr class="bg-gray-{{ $loop->even ? '700' : '600' }}">
                    <td class="border px-4 py-2">{{ $user['name'] }}</td>
                    <td class="border px-4 py-2"><button class="px-4 py-2 bg-blue-600 text-white rounded-md add-user-btn" value={{$user['_id']}}>Agregar</button></td>
                </tr>
                @endforeach
                
            </tbody>
        </table>

        <div class="mt-4">
            <p class="text-sm">Usuarios agregados: <span id="usuarios-agregados" class="font-bold">0</span></p>
        </div>

        <div id="div-agregar" class="mt-6">
            <button id="create-community" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-500">Crear una comunidad</button>
        </div>
        <!-- Link volver -->
        <a href="{{ route('dashboard') }}">
            <button class="px-6 py-2 mt-6 bg-gray-600 text-white rounded-md hover:bg-gray-500">Volver</button>
        </a>
    </div>
    <!-- Funcionalidad agregar usuarios -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let usuariosAgregados = 0;
            const selectedUsers = [];
            const divAgregar = document.getElementById('div-agregar');
            var maximosIntegrantes = document.getElementById('max-integrantes').value;

            document.querySelectorAll('.add-user-btn').forEach(button => {
                button.addEventListener('click', function () {
                    usuariosAgregados++;
                    const userId = button.value;
                    selectedUsers.push(userId);
                    document.getElementById('usuarios-agregados').textContent = usuariosAgregados;

                    // Oculta el botón "Agregar"
                    button.style.display = 'none';

                    // Crea el botón "Quitar"
                    const quitarButton = document.createElement('button');
                    quitarButton.textContent = 'Quitar';
                    quitarButton.className = 'px-4 py-2 bg-red-600 text-white rounded-md quit-user-btn';

                    // Añade el botón "Quitar" en el mismo lugar
                    button.parentElement.appendChild(quitarButton);

                    // Añade el listener para el botón "Quitar"
                    quitarButton.addEventListener('click', function () {
                        usuariosAgregados--;
                        selectedUsers.splice(selectedUsers.indexOf(userId), 1);
                        document.getElementById('usuarios-agregados').textContent = usuariosAgregados;

                        // Oculta el botón "Quitar"
                        quitarButton.style.display = 'none';

                        // Muestra el botón "Agregar" nuevamente
                        button.style.display = 'inline-block';
                    });

                    console.log(selectedUsers);
                });
            });
            document.getElementById('create-community').addEventListener('click', function (e) {
                e.preventDefault();

                // Obtener los valores del formulario
                const nombre = document.getElementById('nombre').value;
                const maxIntegrantes = document.getElementById('max-integrantes').value;
                const tipo = document.getElementById('tipo').value;

                // Crear el objeto de la comunidad
                const communityData = {
                    name: nombre,
                    max_number_users: parseInt(maxIntegrantes),
                    type_community: tipo,
                    users: selectedUsers
                };
                console.log(communityData)

                // Enviar la solicitud POST a la API
                fetch('https://apirestnodejs-jev4.onrender.com/community/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(communityData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(`Error: ${data.error}`);
                    } else {
                        alert('Comunidad creada exitosamente');
                        //window.location.href = '/dashboard'; // Redirigir al dashboard después de crear la comunidad
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Hubo un error al crear la comunidad');
                });
            })
        });

    </script>
</body>
</html>