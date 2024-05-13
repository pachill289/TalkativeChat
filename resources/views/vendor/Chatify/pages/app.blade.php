<x-app-layout>
@include('Chatify::layouts.headLinks')
<div class="messenger">
    {{-- ----------------------Lista de usuarios/grupos---------------------- --}}
    <div class="messenger-listView {{ !!$id ? 'conversation-active' : '' }}">
        {{-- Encabezado y barra de búsqueda --}}
        <div class="m-header">
            <nav>
                <a href="#"><i class="fas fa-comments"></i> <span class="messenger-headTitle">TALKATIVE CHAT</span> </a>
                {{-- botones de encabezado --}}
                <nav class="m-header-right">
                    <a href="#"><i class="fas fa-cog settings-btn"></i></a>
                    <a href="#" class="listView-x"><i class="fas fa-times"></i></a>
                </nav>
            </nav>
            {{-- Entrada de búsqueda --}}
            <input type="text" class="messenger-search" placeholder="Buscar" />
            {{-- Pestañas --}}
            {{-- <div class="messenger-listView-tabs">
                <a href="#" class="active-tab" data-view="users">
                    <span class="far fa-user"></span> Contactos</a>
            </div> --}}
        </div>
        {{-- pestañas y listas --}}
        <div class="m-body contacts-container">
           {{-- Listas [Usuarios/Grupos] --}}
           {{-- ---------------- [ Pestaña de usuario ] ---------------- --}}
           <div class="show messenger-tab users-tab app-scroll" data-view="users">
               {{-- Favoritos --}}
               <div class="favorites-section" style="display: none;">
                <p class="messenger-title"><span>Favoritos</span></p>
                <div class="messenger-favorites app-scroll-hidden"></div>
               </div>
               {{-- Mensajes guardados --}}
               <div style="display: none;">
               <p class="messenger-title" style="display: none;"><span>Tu espacio</span></p>
               {!! view('Chatify::layouts.listItem', ['get' => 'saved']) !!}
               </div>
               {{-- Contacto --}}
               <p class="messenger-title"><span>Todos los mensajes</span></p>
               <div class="listOfContacts" style="width: 100%;height: calc(100% - 272px);position: relative;"></div>
           </div>
             {{-- ---------------- [ Pestaña de búsqueda ] ---------------- --}}
           <div class="messenger-tab search-tab app-scroll" data-view="search">
                {{-- elementos --}}
                <p class="messenger-title"><span>Buscar</span></p>
                <div class="search-records">
                    <p class="message-hint center-el"><span>Escribe para buscar..</span></p>
                </div>
             </div>
        </div>
    </div>

    {{-- ----------------------Área de mensajería---------------------- --}}
    <div class="messenger-messagingView">
        {{-- título del encabezado [nombre de la conversación] y botones --}}
        <div class="m-header m-header-messaging">
            <nav class="chatify-d-flex chatify-justify-content-between chatify-align-items-center">
                {{-- botón de retroceso del encabezado, avatar y nombre de usuario --}}
                <div class="chatify-d-flex chatify-justify-content-between chatify-align-items-center">
                    <a href="#" class="show-listView"><i class="fas fa-arrow-left"></i></a>
                    <div class="avatar av-s header-avatar" style="margin: 0px 10px; margin-top: -5px; margin-bottom: -5px;">
                    </div>
                    <a href="#" class="user-name">{{ config('chatify.name') }}</a>
                </div>
                {{-- botones del encabezado --}}
                <nav class="m-header-right">
                    {{-- <a href="#" class="add-to-favorite"><i class="fas fa-star"></i></a> --}}
                    {{--<a href="/"><i class="fas fa-home"></i></a>--}}
                    <a href="#" class="show-infoSide"><i class="fas fa-info-circle"></i></a>
                </nav>
            </nav>
            {{-- Conexión a Internet --}}
            <div class="internet-connection">
                <span class="ic-connected">Conectado</span>
                <span class="ic-connecting">Conectando...</span>
                <span class="ic-noInternet">Sin acceso a Internet</span>
            </div>
        </div>

        {{-- Área de mensajes --}}
        <div class="m-body messages-container app-scroll">
            <div class="messages">
                <p class="message-hint center-el"><span>Por favor, selecciona un chat para empezar a enviar mensajes</span></p>
            </div>
            {{-- Indicador de escritura --}}
            <div class="typing-indicator">
                <div class="message-card typing">
                    <div class="message">
                        <span class="typing-dots">
                            <span class="dot dot-1"></span>
                            <span class="dot dot-2"></span>
                            <span class="dot dot-3"></span>
                        </span>
                    </div>
                </div>
            </div>

        </div>
        {{-- Formulario de envío de mensajes --}}
        @include('Chatify::layouts.sendForm')
    </div>
    {{-- ---------------------- Lado de información ---------------------- --}}
    <div class="messenger-infoView app-scroll">
        {{-- acciones de navegación --}}
        <nav>
            <p>Detalles del usuario</p>
            <a href="#"><i class="fas fa-times"></i></a>
        </nav>
        {!! view('Chatify::layouts.info')->render() !!}
    </div>
</div>

@include('Chatify::layouts.modals')
@include('Chatify::layouts.footerLinks')
</x-app-layout>
