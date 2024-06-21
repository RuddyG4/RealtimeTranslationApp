<?php

namespace App\Events;

use App\Enums\UserState;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Chat $chat
    ) {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Hacemos Broadcast para cada usuario en el chat del mensaje
        $members = $this->chat->members;
        $channelsToBroadcastOn = [];
        foreach ($members as $member) {
            if ($member->id !== auth()->user()->id) {
                $channelsToBroadcastOn[] = new PrivateChannel('chatUsers.' . $member->id);
            }
        }
        return $channelsToBroadcastOn;
    }
}
