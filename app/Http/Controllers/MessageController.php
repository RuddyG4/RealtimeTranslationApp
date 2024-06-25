<?php

namespace App\Http\Controllers;

use App\Enums\MessageType;
use App\Events\MessageSent;
use App\Models\Message;
use App\Services\MessageTranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $userId = auth()->user()->id;
        $chatId = (int) $request->input('chatId');
        $content = $request->input('content');
        $type = (int) $request->input('type');
        // Verificar si el tipo (type) de mensaje es válido
        if (!in_array($type, MessageType::values(), true)) {
            throw new \Exception('Invalid message type');
        }

        $newMessage = DB::transaction(function () use ($userId, $chatId, $content, $type, $request) {

            $newMessage = Message::create([
                'chat_id' => $chatId,
                'user_id' => $userId,
                'type' => $type,
                'sent_at' => now()
            ]);

            // Guardar el contenido del mensaje en la tabla correspondiente
            switch ($type) {
                case MessageType::TEXT->value:
                    $newMessage->textMessages()->create([
                        'content' => $content,
                        'is_original' => true,
                        'language_id' => auth()->user()->language_id
                    ]);
                    $messageTranslationService = new MessageTranslationService();
                    $messageTranslationService->translateMessageForOnlineUsers($newMessage);
                    $newMessage->load('textMessages');
                    break;

                case MessageType::AUDIO->value:
                    $audio = $request->file('content');
                    $extension = $audio->extension();
                    $relative_path = $audio->store('audios');
                    $path = Storage::url($relative_path);
                    $newMessage->audioMessages()->create([
                        'path' => $path,
                        'relative_path' => $relative_path,
                        'is_original' => true,
                        'language_id' => auth()->user()->language_id,
                        'extension' => $extension,
                    ]);

                    $newMessage->load('audioMessages');
                    break;
                    // TODO: Implement other message types
                default:
                    throw new \Exception('Invalid message type');
            }
            // Translate for online users
            return $newMessage;
        });
        $messageTranslationService = new MessageTranslationService();
        $newMessage = $messageTranslationService->translateMessageForOnlineUsers($newMessage);

        // Broadcast to channels for online users
        broadcast(new MessageSent($newMessage))->toOthers();

        return response()->json([
            'message' => $newMessage,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
