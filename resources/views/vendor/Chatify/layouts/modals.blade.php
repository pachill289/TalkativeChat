{{-- ---------------------- Caja modal de imagen ---------------------- --}}
<div id="imageModalBox" class="imageModal">
    <span class="imageModal-close">&times;</span>
    <img class="imageModal-content" id="imageModalBoxSrc">
</div>

{{-- ---------------------- Modal de eliminación ---------------------- --}}
<div class="app-modal" data-name="delete">
    <div class="app-modal-container">
        <div class="app-modal-card" data-name="delete" data-modal='0'>
            <div class="app-modal-header">¿Estás seguro de que quieres eliminar esto?</div>
            <div class="app-modal-body">Esta acción no se puede deshacer</div>
            <div class="app-modal-footer">
                <a href="javascript:void(0)" class="app-btn cancel">Cancelar</a>
                <a href="javascript:void(0)" class="app-btn a-btn-danger delete">Eliminar</a>
            </div>
        </div>
    </div>
</div>

{{-- ---------------------- Modal de alerta ---------------------- --}}
<div class="app-modal" data-name="alert">
    <div class="app-modal-container">
        <div class="app-modal-card" data-name="alert" data-modal='0'>
            <div class="app-modal-header"></div>
            <div class="app-modal-body"></div>
            <div class="app-modal-footer">
                <a href="javascript:void(0)" class="app-btn cancel">Cancelar</a>
            </div>
        </div>
    </div>
</div>

{{-- ---------------------- Modal de ajustes ---------------------- --}}
<div class="app-modal" data-name="settings">
    <div class="app-modal-container">
        <div class="app-modal-card" data-name="settings" data-modal='0'>
            <form id="update-settings" action="{{ route('avatar.update') }}" enctype="multipart/form-data" method="POST">
                @csrf
                {{-- <div class="app-modal-header">Actualizar ajustes de perfil</div> --}}
                <div class="app-modal-body">
                    {{-- Actualizar avatar de perfil --}}
                    <div class="avatar av-l upload-avatar-preview chatify-d-flex"
                    style="background-image: url('{{ Chatify::getUserWithAvatar(Auth::user())->avatar }}');"
                    ></div>
                    <p class="upload-avatar-details"></p>
                    <label class="app-btn a-btn-primary update" style="background-color:{{$messengerColor}}">
                        Subir Nueva
                        <input class="upload-avatar chatify-d-none" accept="image/*" name="avatar" type="file" />
                    </label>
                    {{-- Modo oscuro/claro --}}
                    <p class="divider"></p>
                    <p class="app-modal-header">Modo Oscuro <span class="
                        {{ Auth::user()->dark_mode > 0 ? 'fas' : 'far' }} fa-moon dark-mode-switch"
                         data-mode="{{ Auth::user()->dark_mode > 0 ? 1 : 0 }}"></span></p>
                    {{-- Cambiar color del chat --}}
                    <p class="divider"></p>
                    {{-- <p class="app-modal-header">Cambiar Color de {{ config('chatify.name') }}</p> --}}
                    <div class="update-messengerColor">
                    @foreach (config('chatify.colors') as $color)
                        <span style="background-color: {{ $color}}" data-color="{{$color}}" class="color-btn"></span>
                        @if (($loop->index + 1) % 5 == 0)
                            <br/>
                        @endif
                    @endforeach
                    </div>
                </div>
                <div class="app-modal-footer">
                    <a href="javascript:void(0)" class="app-btn cancel">Cancelar</a>
                    <input type="submit" class="app-btn a-btn-success update" value="Guardar Cambios" />
                </div>
            </form>
        </div>
    </div>
</div>
