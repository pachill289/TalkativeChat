<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Información del perfil') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Actualiza la información del perfil de tu cuenta y la dirección de correo electrónico.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" id="profileForm" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')
        <input type="hidden" id="userId" name="userId" value="{{ $user->id }}">

        <div>
            <x-input-label for="name" :value="__('Nombre')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Correo electrónico')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Tu dirección de correo electrónico no está verificada.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Haz clic aquí para reenviar el correo de verificación.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('Se ha enviado un nuevo enlace de verificación a tu dirección de correo electrónico.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Guardar') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Guardado.') }}</p>
            @endif
        </div>
    </form>

    <!-- Avatar Section -->
    <div class="mt-4">
        <x-input-label for="avatar" :value="__('Avatar')" />
        <input type="file" id="avatar" name="avatar" accept="image/*" class="block mt-1 w-full">
        <x-input-error :messages="$errors->get('avatar')" class="mt-2" />
    </div>

    <!-- Preview Panel -->
    <div id="previewPanel" class="mt-4 flex flex-col items-center">
        <div class="w-32 h-32 rounded-full bg-gray-200 flex items-center justify-center">
            <img id="avatarPreview" src="{{ $avatarUrl }}" alt="Avatar Preview" class="rounded-full w-32 h-32 object-cover" style="display: block;">
        </div>
        <div class="mt-2">
            <button type="button" id="uploadButton" class="bg-blue-500 text-white px-4 py-2 rounded">Cargar</button>
            <button type="button" id="removeButton" class="bg-red-500 text-white px-4 py-2 rounded">Quitar</button>
        </div>
    </div>
</section>

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
    const avatarPreview = document.getElementById('avatarPreview');
    const uploadButton = document.getElementById('uploadButton');
    const removeButton = document.getElementById('removeButton');
    let avatarRef;
    let avatarUrl = '{{ $avatarUrl }}'; // Obtén la URL del avatar actual

    // Muestra la imagen por defecto o la imagen actual
    if (avatarUrl) {
        avatarPreview.src = avatarUrl; // Utiliza la URL actual si existe
    } else {
        avatarPreview.src = 'https://upload.wikimedia.org/wikipedia/commons/1/14/9-94702_user-outline-icon-clipart-png-download-profile-icon.png'; // URL por defecto
    }

    avatarInput.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const storageRef = firebase.storage().ref();
            avatarRef = storageRef.child('avatars/' + file.name);
            avatarRef.put(file).then((snapshot) => {
                snapshot.ref.getDownloadURL().then((url) => {
                    avatarUrl = url; // Actualiza la URL del avatar solo si se carga una nueva imagen
                    avatarPreview.src = url; // Muestra la nueva imagen en el panel de vista previa
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
        avatarUrl = ''; // Elimina la URL del avatar si se elimina la imagen
        if (avatarRef) {
            avatarRef.delete().then(() => {
                alert('Imagen eliminada.');
            }).catch((error) => {
                console.error('Error deleting the avatar:', error);
            });
        }
    });

    document.getElementById('profileForm').addEventListener('submit', async function(event) {
        event.preventDefault(); // Evita que el formulario se envíe de inmediato

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
        } else if (!avatarUrl) {
            avatarUrl = 'https://upload.wikimedia.org/wikipedia/commons/1/14/9-94702_user-outline-icon-clipart-png-download-profile-icon.png';
        }

        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'avatar_url';
        hiddenInput.value = avatarUrl;
        document.getElementById('profileForm').appendChild(hiddenInput);

        this.submit();
    });
</script>