<?php

namespace App\Http\Controllers\vendor\Chatify;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;
use App\Models\User;
use App\Models\ChMessage as Message;
use App\Models\ChFavorite as Favorite;
use Chatify\Facades\ChatifyMessenger as Chatify;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;

class MessagesController extends Controller
{
    protected $perPage = 30;

    /**
     * Authenticate the connection for pusher
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function pusherAuth(Request $request)
    {
        return Chatify::pusherAuth(
            $request->user(),
            Auth::user(),
            $request['channel_name'],
            $request['socket_id']
        );
    }

    /**
     * Returning the view of the app with the required data.
     *
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index( $id = null)
    {
        $messenger_color = Auth::user()->messenger_color;
        return view('Chatify::pages.app', [
            'id' => $id ?? 0,
            'messengerColor' => $messenger_color ? $messenger_color : Chatify::getFallbackColor(),
            'dark_mode' => Auth::user()->dark_mode < 1 ? 'light' : 'dark',
        ]);
    }


    /**
     * Fetch data (user, favorite.. etc).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function idFetchData(Request $request)
    {
        // Registrar el tiempo inicial
        $startTime = microtime(true);

        //$favorite = Chatify::inFavorite($request['id']);
        $fetch = User::where('id', $request['id'])->first();
        $userAvatar = null;

        if ($fetch) {
            $userAvatar = Chatify::getUserWithAvatar($fetch)->avatar;
            // Si la imagen es una URL de imgbb, obtén la URL directamente
            if (Str::startsWith($userAvatar, 'https://i.ibb.co/')) {
                $imageId = basename(parse_url($userAvatar, PHP_URL_PATH));
                $userAvatar = $this->getImageFromImgbb($imageId);
            }
        }

        // Registrar el tiempo final y calcular el tiempo de ejecución
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        return Response::json([
            //'favorite' => $favorite,
            'fetch' => $fetch ?? null,
            'user_avatar' => $userAvatar ?? null,
            'execution_time' => $executionTime // Tiempo de ejecución en segundos
        ]);
    }


    /**
     * Obtiene la URL de una imagen en imgbb a partir de su ID.
     *
     * @param string $imageId
     * @return string|null
     */
    public function getImageFromImgbb($imageId)
    {
        $client = new Client();
        $response = $client->get("https://api.imgbb.com/1/image/$imageId?key=053648b06603be2d33ae1491a2b5eb18");

        $data = json_decode($response->getBody(), true);

        return $data['data']['url'] ?? null;
    }


    /**
     * This method to make a links for the attachments
     * to be downloadable.
     *
     * @param string $fileName
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|void
     */
    public function download($fileName)
    {
        /*$filePath = config('chatify.attachments.folder') . '/' . $fileName;
        if (Chatify::storage()->exists($filePath)) {
            return Chatify::storage()->download($filePath);
        }
        return abort(404, "Sorry, File does not exist in our server or may have been deleted!");*/
    }

    /**
     * Send a message to database
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function send(Request $request)
    {
        // default variables
        $error = (object)[
            'status' => 0,
            'message' => null
        ];
        $attachment = null;
        $attachment_title = null;

        // if there is attachment [file]
        if ($request->hasFile('file')) {
            // allowed extensions
            $allowed_images = Chatify::getAllowedImages();
            $allowed_files  = Chatify::getAllowedFiles();
            $allowed        = array_merge($allowed_images, $allowed_files);

            $file = $request->file('file');
            // check file size
            if ($file->getSize() < Chatify::getMaxUploadSize()) {
                if (in_array(strtolower($file->extension()), $allowed)) {
                    // get attachment name
                    $attachment_title = $file->getClientOriginalName();
                    // upload attachment to imgbb
                    $client = new Client();
                    $response = $client->post('https://api.imgbb.com/1/upload', [
                        'multipart' => [
                            [
                                'name'     => 'image',
                                'contents' => fopen($file->path(), 'r'),
                                'filename' => $attachment_title
                            ],
                            [
                                'name' => 'key',
                                'contents' => '053648b06603be2d33ae1491a2b5eb18'
                            ]
                        ]
                    ]);

                    $data = json_decode($response->getBody(), true);
                    $attachment = $data['data']['url'];
                } else {
                    $error->status = 1;
                    $error->message = "File extension not allowed!";
                }
            } else {
                $error->status = 1;
                $error->message = "File size you are trying to upload is too large!";
            }
        }

        if (!$error->status) {
            // Prepare data to send to the API
            $messageData = [
                'from_id' => Auth::user()->id,
                'to_id' => $request['id'],
                'body' => htmlentities(trim($request['message']), ENT_QUOTES, 'UTF-8'),
                'attachment' => $attachment ? json_encode((object)[
                    'new_name' => $attachment,
                    'old_name' => htmlentities(trim($attachment_title), ENT_QUOTES, 'UTF-8'),
                ]) : null,
            ];

            // Send message data to the API
            try {
                // Obtén la URL base desde el archivo .env
                $apiBaseUrl = env('API_BASE_URL');                
                // Define el endpoint específico
                $endpoint = '/message/add';
                // Construye la URL completa
                $apiUrl = $apiBaseUrl . $endpoint;               
                // Crear un nuevo cliente GuzzleHttp
                $client = new Client();                
                // Hacer la solicitud POST
                $response = $client->post($apiUrl, [
                    'json' => $messageData
                ]);
                
                // Decodificar la respuesta de la API
                $apiResponse = json_decode($response->getBody(), true);
            } catch (\Exception $e) {
                $error->status = 1;
                $error->message = $e->getMessage();
            }

            if (isset($apiResponse) && !$error->status) {
                $messageHtml = $this->generateMessageHtml($apiResponse);

                if (Auth::user()->id != $request['id']) {
                    Chatify::push("private-chatify.".$request['id'], 'messaging', [
                        'from_id' => Auth::user()->id,
                        'to_id' => $request['id'],
                        'message' => $messageHtml
                    ]);
                }
            }
        }

        // send the response
        return Response::json([
            'status' => '200',
            'error' => $error,
            'message' => $messageHtml ?? null,
            'tempID' => $request['temporaryMsgId'],
            'debug_info' => $apiResponse ?? null,
        ]);
    }
    private function generateMessageHtml($apiResponse)
    {
        $messageId = $apiResponse['_id'];
        $messageBody = $apiResponse['body'];
        $createdAt = $apiResponse['created_at'];
        $timeAgo = 'hace 1 segundo'; // You may need to implement a function to calculate this
        $isSender = true; // Assuming the current user is the sender
        $seen = '<span class="fas fa-check" seen></span>';

        $attachmentHtml = '';
        if (!empty($apiResponse['attachment'])) {
            $attachmentData = json_decode($apiResponse['attachment']);
            if ($attachmentData && isset($attachmentData->new_name)) {
                $attachmentHtml = "<div class='image-wrapper' style='text-align: end'>
                    <div class='image-file chat-image' style='background-image: url({$attachmentData->new_name})'>
                        <div>{$attachmentData->old_name}</div>
                    </div>
                    <div style='margin-bottom:5px'>
                        <span data-time='{$createdAt}' class='message-time'>
                            {$seen} <span class='time'>{$timeAgo}</span>
                        </span>
                    </div>
                </div>";
            }
        }

        return "<div class=\"message-card mc-sender\" data-id=\"{$messageId}\">
            <div class=\"actions\">
                <i class=\"fas fa-trash delete-btn\" data-id=\"{$messageId}\"></i>
            </div>
            <div class=\"message-card-content\">
                <div class=\"message\">
                    {$messageBody}
                    <span data-time='{$createdAt}' class='message-time'>
                        {$seen} <span class='time'>{$timeAgo}</span>
                    </span>
                </div>
                {$attachmentHtml}
            </div>
        </div>";
    }




    /**
     * fetch [user/group] messages from database
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function fetch(Request $request)
    {
        $response = [
            'total' => 0,
            'last_page' => 1,
            'last_message_id' => null,
            'messages' => '',
            'debug_info' => null,
        ];

        // Obtener el ID del usuario autenticado
        $authUserId = Auth::id();

        // Obtener el ID del otro usuario desde la solicitud
        $userId = $request->id;

        // Realizar la solicitud a la API
        $apiBaseUrl = env('API_BASE_URL');
        $endpoint = "/fetchMessages/{$authUserId}/{$userId}";
        $apiUrl = $apiBaseUrl . $endpoint;

        $apiResponse = Http::get($apiUrl);

        if ($apiResponse->successful()) {
            $messages = $apiResponse->json();

            // Contar los mensajes y asignar el total
            $response['total'] = count($messages);

            // Establecer last_message_id como el _id del último mensaje
            $response['last_message_id'] = isset($messages[count($messages) - 1]['_id']) ? $messages[count($messages) - 1]['_id'] : null;

            // Construir el HTML de los mensajes
            foreach ($messages as $message) {
                // Determinar si el mensaje es enviado o recibido
                $isSender = $message['from_id'] == $authUserId;
                $messageClass = $isSender ? 'mc-sender' : 'mc-receiver';

                $response['messages'] .= "<div class=\"message-card {$messageClass}\" data-id=\"{$message['_id']}\">\n";

                // Mostrar el botón de eliminar solo si el usuario es el remitente
                if ($isSender) {
                    $response['messages'] .= "<div class=\"actions\">\n";
                    $response['messages'] .= "<i class=\"fas fa-trash delete-btn\" data-id=\"{$message['_id']}\"></i>\n";
                    $response['messages'] .= "</div>\n";
                }

                $response['messages'] .= "<div class=\"message-card-content\">\n";
                $response['messages'] .= "<div class=\"message\">\n";
                $response['messages'] .= "{$message['body']}\n";
                $response['messages'] .= "<span data-time='{$message['created_at']}' class='message-time'>\n";
                $response['messages'] .= $isSender ? "<span class='fas fa-check' seen></span> " : "";
                $response['messages'] .= "<span class='time'>hace X horas</span>\n";
                $response['messages'] .= "</span>\n";
                $response['messages'] .= "</div>\n";

                // Incluir la imagen si existe
                if (!empty($message['attachment'])) {
                    $attachmentData = json_decode($message['attachment']);
                    if ($attachmentData && isset($attachmentData->new_name)) {
                        $response['messages'] .= "<div class='image-wrapper' style='text-align: end'>\n";
                        $response['messages'] .= "<div class='image-file chat-image' style='background-image: url({$attachmentData->new_name})'>\n";
                        $response['messages'] .= "<div>{$attachmentData->old_name}</div>\n";
                        $response['messages'] .= "</div>\n";
                        $response['messages'] .= "<div style='margin-bottom:5px'>\n";
                        $response['messages'] .= "<span data-time='{$message['created_at']}' class='message-time'>\n";
                        $response['messages'] .= $isSender ? "<span class='fas fa-check' seen></span> " : "";
                        $response['messages'] .= "<span class='time'>hace X horas</span>\n";
                        $response['messages'] .= "</span>\n";
                        $response['messages'] .= "</div>\n";
                        $response['messages'] .= "</div>\n";
                    }
                }

                $response['messages'] .= "</div>\n";
                $response['messages'] .= "</div>\n";
            }
        } else {
            // Manejar la respuesta no exitosa de la API
            $response['messages'] = '<p class="message-hint center-el"><span>Error al obtener mensajes</span></p>';
        }

        // Devolver la respuesta en formato JSON
        return response()->json($response);
    }

    /**
     * Make messages as seen
     *
     * @param Request $request
     * @return JsonResponse|void
     */
    public function seen(Request $request)
    {
        // make as seen
        $seen = Chatify::makeSeen($request['id']);
        // send the response
        return Response::json([
            'status' => $seen,
        ], 200);
    }

    /**
     * Get contacts list
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getContacts(Request $request)
    {
        // Registrar el tiempo inicial
        $startTime = microtime(true);

        $userId = Auth::user()->id;
        $apiUrl = 'http://localhost:3000/getContacts/' . $userId;
        $apiResponse = Http::get($apiUrl);

        if ($apiResponse->successful()) {
            $users = $apiResponse->json();
            $contacts = '';

            if (is_array($users['data']) && count($users['data']) > 0) {
                foreach ($users['data'] as $user) {
                    $authUserId = Auth::id(); // Obtiene el ID del usuario autenticado
                    $lastMessage = $this->fetchLastMessage($authUserId, $user['id']);
                    $contacts .= $this->formatContactItem($user, $lastMessage);
                }
            } else {
                $contacts = '<p class="message-hint center-el"><span>Tu lista de contactos está vacía</span></p>';
            }

            // Registrar el tiempo final y calcular el tiempo de ejecución
            $endTime = microtime(true);
            $executionTime = $endTime - $startTime;

            return response()->json([
                'contacts' => $contacts,
                'total' => $users['total'],
                'last_page' => $users['last_page'],
                'debug_info' => $users,
                'execution_time' => $executionTime // Tiempo de ejecución en segundos
            ], 200);
        } else {
            // Manejar la respuesta no exitosa de la API
            $endTime = microtime(true);
            $executionTime = $endTime - $startTime;

            return response()->json([
                'contacts' => '<p class="message-hint center-el"><span>Error al obtener contactos</span></p>',
                'total' => 0,
                'last_page' => 1,
                'debug_info' => 'Error al realizar la solicitud a la API',
                'execution_time' => $executionTime // Tiempo de ejecución en segundos
            ], 500);
        }
    }


    private function fetchLastMessage($authUserId, $userId)
    {
        $apiUrl = 'http://localhost:3000/getLastMessage/' . $authUserId . '/' . $userId;
        $apiResponse = Http::get($apiUrl);

        if ($apiResponse->successful()) {
            $messages = $apiResponse->json();
            return !empty($messages[0]) ? $messages[0] : null;
        }

        return null;
    }


    private function formatContactItem($user, $lastMessage)
    {
        $userId = $user['id'] ?? '';
        $userName = $user['name'] ?? 'Usuario desconocido';
        $userAvatar = $user['avatar'] ?? 'default-avatar.png';
        $maxCreatedAt = $user['max_created_at'] ?? '';
        $lastMessageText = !empty($lastMessage['attachment']) && $lastMessage['attachment'] == 'image' ? 'Archivo adjunto' : $lastMessage['body'];
        $contactItemTime = $this->formatTimeAgo($maxCreatedAt);

        return "<table class=\"messenger-list-item\" data-contact=\"{$userId}\">
            <tr data-action=\"0\">
                <td style=\"position: relative\">
                    <div class=\"avatar av-m\"
                        style=\"background-image: url('http://localhost:8000/storage/users-avatar/{$userAvatar}');\">
                    </div>
                </td>
                <td>
                    <p data-id=\"{$userId}\" data-type=\"user\">
                        {$userName}
                        <span class=\"contact-item-time\" data-time=\"{$maxCreatedAt}\">{$contactItemTime}</span>
                    </p>
                    <span>
                        <span class=\"fas fa-file\"></span> {$lastMessageText}
                    </span>
                </td>
            </tr>
        </table>\n\n\n\n\n";
    }

    private function formatTimeAgo($time)
    {
        // Implementa la lógica para convertir la fecha y hora en el formato deseado
        return 'hace X tiempo'; // Reemplaza esto con la lógica adecuada
    }


    /**
     * Update user's list item data
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateContactItem(Request $request)
    {
        // Get user data
        $user = User::where('id', $request['user_id'])->first();
        if(!$user){
            return Response::json([
                'message' => 'Usuario no encontrado',
            ], 401);
        }
        $contactItem = Chatify::getContactItem($user);

        // send the response
        return Response::json([
            'contactItem' => $contactItem,
            'users' => $user,
        ], 200);
    }

    /**
     * Put a user in the favorites list
     *
     * @param Request $request
     * @return JsonResponse|void
     */
    public function favorite(Request $request)
    {
        /*$userId = $request['user_id'];
        // check action [star/unstar]
        $favoriteStatus = Chatify::inFavorite($userId) ? 0 : 1;
        Chatify::makeInFavorite($userId, $favoriteStatus);

        // send the response
        return Response::json([
            'status' => @$favoriteStatus,
        ], 200);*/
    }

    /**
     * Get favorites list
     *
     * @param Request $request
     * @return JsonResponse|void
     */
    public function getFavorites(Request $request)
    {
        if ($request->isMethod('post')) {
        /*$favoritesList = null;
        $favorites = Favorite::where('user_id', Auth::user()->id);
        foreach ($favorites->get() as $favorite) {
            // get user data
            $user = User::where('id', $favorite->favorite_id)->first();
            $favoritesList .= view('Chatify::layouts.favorite', [
                'user' => $user,
            ]);
        }
        // send the response
        return Response::json([
            'count' => $favorites->count(),
            'favorites' => $favorites->count() > 0
                ? $favoritesList
                : 0,
        ], 200);*/
        }
    }

    /**
     * Search in messenger
     *
     * @param Request $request
     * @return JsonResponse|void
     */
    public function search(Request $request)
    {
        $getRecords = null;
        $input = trim(filter_var($request['input']));
        $records = User::where('id','!=',Auth::user()->id)
                    ->where('name', 'LIKE', "%{$input}%")
                    ->paginate($request->per_page ?? $this->perPage);
        foreach ($records->items() as $record) {
            $getRecords .= view('Chatify::layouts.listItem', [
                'get' => 'search_item',
                'user' => Chatify::getUserWithAvatar($record),
            ])->render();
        }
        if($records->total() < 1){
            $getRecords = '<p class="message-hint center-el"><span>Nada para mostrar</span></p>';
        }
        // send the response
        return Response::json([
            'records' => $getRecords,
            'total' => $records->total(),
            'last_page' => $records->lastPage()
        ], 200);
    }

    /**
     * Get shared photos
     *
     * @param Request $request
     * @return JsonResponse|void
     */
    public function sharedPhotos(Request $request)
    {
        /*$sharedPhotos = '';
        $userId = $request['user_id'];

        // Realizar la solicitud a la API
        $apiBaseUrl = env('API_BASE_URL');
        $endpoint = '/getSharedPhotos/' . $userId;;
        $apiUrl = $apiBaseUrl . $endpoint;

        $apiResponse = Http::get($apiUrl);

        if ($apiResponse->successful()) {
            $shared = $apiResponse->json();

            // Construir el HTML de las fotos compartidas
            for ($i = 0; $i < count($shared); $i++) {
                $sharedPhotos .= "<div class=\"shared-photo chat-image\" style=\"background-image: url('{$shared[$i]}')\"></div>\n";
            }

            // Devolver la respuesta en formato JSON
            return response()->json([
                'shared' => $sharedPhotos,
                'debug_info' => $sharedPhotos,
            ], 200);
        } else {
            // Manejar la respuesta no exitosa de la API
            return response()->json([
                'shared' => '<p class="message-hint"><span>Error al obtener fotos compartidas</span></p>',
                'debug_info' => '<p class="message-hint"><span>Error al obtener fotos compartidas</span></p>',
            ], 500);
        }*/
    }


    /**
     * Delete conversation
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteConversation(Request $request)
    {
        // delete
        $delete = Chatify::deleteConversation($request['id']);

        // send the response
        return Response::json([
            'deleted' => $delete ? 1 : 0,
        ], 200);
    }

    /**
     * Delete message
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteMessage(Request $request)
    {
        // delete
        $delete = Chatify::deleteMessage($request['id']);

        // send the response
        return Response::json([
            'deleted' => $delete ? 1 : 0,
        ], 200);
    }

    public function updateSettings(Request $request)
    {
        $msg = null;
        $error = $success = 0;

        // dark mode
        if ($request['dark_mode']) {
            $request['dark_mode'] == "dark"
                ? User::where('id', Auth::user()->id)->update(['dark_mode' => 1])  // Make Dark
                : User::where('id', Auth::user()->id)->update(['dark_mode' => 0]); // Make Light
        }

        // If messenger color selected
        if ($request['messengerColor']) {
            $messenger_color = trim(filter_var($request['messengerColor']));
            User::where('id', Auth::user()->id)
                ->update(['messenger_color' => $messenger_color]);
        }
        // if there is a [file]
        if ($request->hasFile('avatar')) {
            // allowed extensions
            $allowed_images = Chatify::getAllowedImages();

            $file = $request->file('avatar');
            // check file size
            if ($file->getSize() < Chatify::getMaxUploadSize()) {
                if (in_array(strtolower($file->extension()), $allowed_images)) {
                    // delete the older one
                    if (Auth::user()->avatar != config('chatify.user_avatar.default')) {
                        $avatar = Auth::user()->avatar;
                        if (Chatify::storage()->exists($avatar)) {
                            Chatify::storage()->delete($avatar);
                        }
                    }
                    // upload
                    $avatar = Str::uuid() . "." . $file->extension();
                    $update = User::where('id', Auth::user()->id)->update(['avatar' => $avatar]);
                    $file->storeAs(config('chatify.user_avatar.folder'), $avatar, config('chatify.storage_disk_name'));
                    $success = $update ? 1 : 0;
                } else {
                    $msg = "Extensión de archivo no permitida";
                    $error = 1;
                }
            } else {
                $msg = "Tamaño de archivo muy grande";
                $error = 1;
            }
        }

        // send the response
        return Response::json([
            'status' => $success ? 1 : 0,
            'error' => $error ? 1 : 0,
            'message' => $error ? $msg : 0,
        ], 200);
    }

    /**
     * Set user's active status
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function setActiveStatus(Request $request)
    {
        $activeStatus = $request['status'] > 0 ? 1 : 0;
        $status = User::where('id', Auth::user()->id)->update(['active_status' => $activeStatus]);
        return Response::json([
            'status' => $status,
        ], 200);
    }
}
