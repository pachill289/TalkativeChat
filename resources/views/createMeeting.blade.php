<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Video meeting</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-900 h-screen">
    @auth
    <div class="container mx-auto p-4 pt-6 md:p-6 lg:p-12">
        <input type="text" id="linkUrl" value="" placeholder="Ingresa el link de la reunión" class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500">
        <div class="flex justify-center mt-4">
            <button id="join-btn1" onclick="joinUserMeeting()" class="bg-blue-800 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Unirse a la reunión
            </button>
            @if(Auth::User())
            <a href="{{url('crearTokenVideollamada')}}"><button id="join-btn2" class="bg-blue-800 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded ml-4">
                Crear reunión
            </button></a>
            @endif
        </div>
    </div>
    
    <canvas id="video-canvas" style="display: none;"></canvas>
    <video  id="video-element" style="display: none;" autoplay></video>

    <script>
        function joinUserMeeting()
        {
            var link = document.getElementById('linkUrl').value;
            if(link.trim() == '' || link.length < 1)
            {
                alert('Por favor introduce un link válido');
                return;
            }
            else
            {
                window.location.href = link;
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            const videoElement = document.getElementById('video-element');
            const videoCanvas = document.getElementById('video-canvas');
            const canvasContext = videoCanvas.getContext('2d');

            // Solicitar acceso a la cámara del usuario
            navigator.mediaDevices.getUserMedia({ video: true })
            .then((stream) => {
                videoElement.style.display = 'block';
                videoElement.srcObject = stream;
        
                videoElement.onloadedmetadata = function() {
                    videoElement.play();
                };
                
                // Dibujar el video en el canvas
                videoElement.addEventListener('play', function() {
                    const drawVideoOnCanvas = function() {
                        if (!videoElement.paused && !videoElement.ended) {
                            canvasContext.drawImage(videoElement, 0, 0, videoCanvas.width, videoCanvas.height);
                            requestAnimationFrame(drawVideoOnCanvas);
                        }
                    };
                    drawVideoOnCanvas();
                });
            })
            .catch((err) => {
                console.error('Error al acceder al medio de videollamada.', err);
            });
        });
    </script>
    @else
    <div class="fixed hidden inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full" id="modal">
        <div class="relative p-4 w-full max-w-md h-full md:h-auto">
          <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="p-6 text-lg">
              <h3 class="mb-5 text-lg font-bold text-gray-900 dark:text-white">Inicia sesión primero</h3>
              <p class="text-gray-500 dark:text-gray-400">Debes iniciar sesión para acceder a esta función.</p>
            </div>
          </div>
        </div>
      </div>
      
      <script>
        document.addEventListener("DOMContentLoaded", function() {
          document.getElementById("modal").classList.remove("hidden");
        });
      </script>
</body>
</html>
@endauth