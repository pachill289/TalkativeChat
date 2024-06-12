<div class="messenger-sendCard">
    <form id="message-form" method="POST" action="{{ route('send.message') }}" enctype="multipart/form-data">
        @csrf
        <label><span class="fas fa-plus-circle"></span><input disabled='disabled' type="file" class="upload-attachment" name="file" accept=".{{implode(', .',config('chatify.attachments.allowed_images'))}}, .{{implode(', .',config('chatify.attachments.allowed_files'))}}" /></label>
        <button class="emoji-button"></span><span class="fas fa-smile"></span></button>
        <x-nav-link :href="route('meeting')" :active="request()->routeIs('meeting')">
            <span class="fas fa-video"></span>
        </x-nav-link>
        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
            
        </div>
        <textarea readonly='readonly' name="message" class="m-send app-scroll" placeholder="Escribe un mensaje.."></textarea>
        <button disabled='disabled' class="send-button"><span class="fas fa-paper-plane"></span></button>
    </form>
    
</div>
