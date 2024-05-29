<div class="messenger-sendCard">
    <form id="message-form" method="POST" action="{{ route('send.message') }}" enctype="multipart/form-data">
        @csrf
        <label><span class="fas fa-plus-circle"></span><input disabled='disabled' type="file" class="upload-attachment" name="file" accept=".{{implode(', .',config('chatify.attachments.allowed_images'))}}, .{{implode(', .',config('chatify.attachments.allowed_files'))}}" /></label>
        <button class="emoji-button"></span><span class="fas fa-smile"></span></button>
        <button id="video-button" class="emoji-button"></span><span class="fas fa-video"></span></button>
        <textarea readonly='readonly' name="message" class="m-send app-scroll" placeholder="Escribe un mensaje.."></textarea>
        <button disabled='disabled' class="send-button"><span class="fas fa-paper-plane"></span></button>
    </form>
    <canvas id="video-canvas" style="display: none;"></canvas>
    <video id="video-element" style="display: none;" autoplay></video>

    <script>
        var i = 0;
        document.getElementById('video-button').addEventListener('click', function(event) {
            event.preventDefault();
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
                if(i == 0)
                {
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
                    // Mostrar el video
                    videoElement.style.display = 'block';
                    i = 1;
                }
                else
                {
                    // Si la cámara ya está activada, detener el stream y ocultar elementos
                    const tracks = stream.getTracks();
                    tracks.forEach(track => track.stop());
                    stream = null;
                    videoElement.style.display = 'none';
                    i = 0;
                }
                
            })
            .catch((err) => {
                console.error('Error al acceder al medio de videollamada.', err);
            });
        });
        </script>
</div>
