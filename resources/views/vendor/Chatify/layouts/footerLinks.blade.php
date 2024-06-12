<script src="https://js.pusher.com/7.2.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@joeattardi/emoji-button@3.0.3/dist/index.min.js"></script>
<script >
    // Gloabl Chatify variables from PHP to JS
    window.chatify = {
        name: "{{ config('chatify.name') }}",
        sounds: {!! json_encode(config('chatify.sounds')) !!},
        allowedImages: {!! json_encode(config('chatify.attachments.allowed_images')) !!},
        allowedFiles: {!! json_encode(config('chatify.attachments.allowed_files')) !!},
        maxUploadSize: {{ Chatify::getMaxUploadSize() }},
        pusher: {!! json_encode(config('chatify.pusher')) !!},
        pusherAuthEndpoint: '{{route("pusher.auth")}}'
    };
    window.chatify.allAllowedExtensions = chatify.allowedImages.concat(chatify.allowedFiles);
</script>

  <!-- Firebase -->
  <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
  <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-auth.js"></script>
  <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-database.js"></script>
  <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-storage.js"></script>
<script>
    console.log("HOLA");
    var firebaseConfig = {
        apiKey: "AIzaSyBXe1GnXkLp1KrPh7zHH8kmLR_6-zkBP3k",
  authDomain: "brilliant-era-407902.firebaseapp.com",
  databaseURL: "https://brilliant-era-407902-default-rtdb.firebaseio.com",
  projectId: "brilliant-era-407902",
  storageBucket: "brilliant-era-407902.appspot.com",
  messagingSenderId: "818550448418",
  appId: "1:818550448418:web:bb9ade8fbac79af401857b"
};

// Inicializar Firebase
firebase.initializeApp(firebaseConfig);

// Referencia a la base de datos de Firebase
var db = firebase.database();

console.log("HOLAS");

</script>
<script src="{{ asset('js/chatify/utils.js') }}"></script>
<script src="{{ asset('js/chatify/code.js') }}"></script>
