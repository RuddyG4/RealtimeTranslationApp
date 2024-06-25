<?php

namespace App\Http\Controllers;

use App\Enums\ChatType;
use App\Events\ChatCreated;
use App\Models\Chat;
use App\Models\User;
use App\Services\MessageTranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserChatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(User $user)
    {
        $chats = $user->chats()
            ->with([
                'members',
                'messages' => function ($query) {
                    $query->with(['translatedText', 'translatedAudio'])
                        ->orderBy('sent_at', 'desc')
                        ->limit(20);
                },
                'latestMessage' => function ($query) {
                    $query->with(['translatedText', 'translatedAudio']);
                }
            ])
            ->orderBy('updated_at', 'desc')
            ->get();
        // Verificar si los mensajes están traducidos al idioma del usuario y traducirlos si no lo estan
        $newMessagesTranslated = false;
        $messageTranslationService = new MessageTranslationService();
        foreach ($chats as $chat) {
            $newMessagesTranslated = ($messageTranslationService->verifyAndTranslate($chat->messages));
        }
        if ($newMessagesTranslated) {
            $chats->load([
                'messages' => function ($query) {
                    $query->with(['translatedText', 'translatedAudio'])
                        ->orderBy('sent_at', 'desc')
                        ->limit(20);
                },
                'latestMessage' => function ($query) {
                    $query->with(['translatedText', 'translatedAudio']);
                }
            ]);
        }

        return response()->json([
            'chats' => $chats,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, User $user)
    {
        $userToChat = $request->input('userToChat');
        $chat = DB::transaction(function () use ($user, $userToChat) {
            $chat = Chat::create([
                'type' => ChatType::PRIVATE,
                'created_by' => $user->id
            ]);
            $chat->members()->attach([$user->id, $userToChat["id"]]);
            $chat->load(['members', 'messages.textMessage']);
            return $chat;
        });
        broadcast(new ChatCreated($chat))->toOthers();

        return response()->json(['chat' => $chat]);
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
