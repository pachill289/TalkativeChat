<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" id="registerForm" class="flex">
        @csrf

        <div class="w-8/3 pr-8">
            <!-- Nombre -->
            <div>
                <x-input-label for="name" :value="__('Nombre')" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Dirección de Correo Electrónico -->
            <div class="mt-4">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Contraseña -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Contraseña')" />

                <x-text-input id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="new-password" />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirmar Contraseña -->
            <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

                <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />

                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                    {{ __('Iniciar sesión') }}
                </a>

                <x-primary-button class="ms-4">
                    {{ __('Crear cuenta') }}
                </x-primary-button>
            </div>
        </div>

        <div class="w-1/3 pl-4">
            <!-- Avatar -->
            <div class="mt-4">
                <x-input-label for="avatar" :value="__('Avatar')" />
                <input type="file" id="avatar" name="avatar" accept="image/*" class="block mt-1 w-full">
                <x-input-error :messages="$errors->get('avatar')" class="mt-2" />
            </div>

            <!-- Panel de previsualización -->
            <div id="previewPanel" class="mt-4 flex flex-col items-center">
                <div class="w-32 h-32 rounded-full bg-gray-200 flex items-center justify-center">
                    <img id="avatarPreview" src="" alt="Avatar Preview" class="rounded-full w-32 h-32 object-cover" style="display: none;">
                </div>
                <div class="mt-2">
                    <button type="button" id="uploadButton" class="bg-blue-500 text-white px-4 py-2 rounded">Cargar</button>
                    <button type="button" id="removeButton" class="bg-red-500 text-white px-4 py-2 rounded">Quitar</button>
                </div>
            </div>
        </div>
    </form>

    <!-- Firebase Configuration -->
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-storage-compat.js"></script>
    <script>
        // Configura tu Firebase aquí
        const firebaseConfig = {
        apiKey: "AIzaSyBXe1GnXkLp1KrPh7zHH8kmLR_6-zkBP3k",
        authDomain: "brilliant-era-407902.firebaseapp.com",
        databaseURL: "https://brilliant-era-407902-default-rtdb.firebaseio.com",
        projectId: "brilliant-era-407902",
        storageBucket: "brilliant-era-407902.appspot.com",
        messagingSenderId: "818550448418",
        appId: "1:818550448418:web:bb9ade8fbac79af401857b"
    };

        // Inicializa Firebase
        firebase.initializeApp(firebaseConfig);

        const avatarInput = document.getElementById('avatar');
        const previewPanel = document.getElementById('previewPanel');
        const avatarPreview = document.getElementById('avatarPreview');
        const uploadButton = document.getElementById('uploadButton');
        const removeButton = document.getElementById('removeButton');
        let avatarRef;
        let avatarUrl = '';

        avatarInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    avatarPreview.src = e.target.result;
                    avatarPreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                avatarPreview.src = '';
                avatarPreview.style.display = 'none';
            }
        });

        uploadButton.addEventListener('click', function() {
            const file = avatarInput.files[0];
            if (file) {
                const storageRef = firebase.storage().ref();
                avatarRef = storageRef.child('avatars/' + file.name);
                avatarRef.put(file).then((snapshot) => {
                    snapshot.ref.getDownloadURL().then((url) => {
                        avatarUrl = url;
                        //alert('Imagen cargada exitosamente.');
                        console.log('Imagen cargada exitosamente.');
                    });
                }).catch((error) => {
                    console.error('Error uploading the avatar:', error);
                });
            }
        });

        removeButton.addEventListener('click', function() {
            avatarInput.value = '';
            avatarPreview.src = '';
            avatarPreview.style.display = 'none';
            avatarUrl = '';
            if (avatarRef) {
                avatarRef.delete().then(() => {
                    alert('Imagen eliminada.');
                }).catch((error) => {
                    console.error('Error deleting the avatar:', error);
                });
            }
        });

        document.getElementById('registerForm').addEventListener('submit', function(event) {
            if (avatarUrl) {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'avatar_url';
                hiddenInput.value = avatarUrl;
                document.getElementById('registerForm').appendChild(hiddenInput);
            }
        });
    </script>
    <script>
    document.getElementById('registerForm').addEventListener('submit', async function(event) {
    event.preventDefault(); // Evita que el formulario se envíe de inmediato

    // Subir la imagen a Firebase si está presente
    if (avatarInput.files.length > 0) {
        const file = avatarInput.files[0];
        const storageRef = firebase.storage().ref();
        avatarRef = storageRef.child('avatars/' + file.name);
        try {
            const snapshot = await avatarRef.put(file);
            avatarUrl = await snapshot.ref.getDownloadURL();
            alert('Imagen cargada exitosamente.');
        } catch (error) {
            console.error('Error uploading the avatar:', error);
            return; // Detiene la ejecución si hay un error al subir la imagen
        }
    } else {
        // Si no hay imagen, usar el enlace predeterminado
        avatarUrl = 'https://upload.wikimedia.org/wikipedia/commons/1/14/9-94702_user-outline-icon-clipart-png-download-profile-icon.png';
    }

    // Agregar la URL de la imagen al formulario
    const hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.name = 'avatar_url';
    hiddenInput.value = avatarUrl;
    document.getElementById('registerForm').appendChild(hiddenInput);

    // Envía el formulario
    this.submit();
});

</script>

</x-guest-layout>
