<?php

namespace App\Events;

use App\Enums\UserState;
use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The message instance.
     *
     * @var \App\Models\Message
     */
    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Hacemos Broadcast para cada usuario en el chat del mensaje
        $chat = $this->message->chat;
        $users = $chat->members;
        $channelsToBroadcastOn = [];
        foreach ($users as $user) {
            // Se hace la transmisión a los usuarios que estén activos (no offline)
            if ($user->id !== auth()->user()->id && $user->state !== UserState::OFFLINE->value) {
                $channelsToBroadcastOn[] = new PrivateChannel('chatUsers.' . $user->id);
            }
        }
        return $channelsToBroadcastOn;
    }
}
