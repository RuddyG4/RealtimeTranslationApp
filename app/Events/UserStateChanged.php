<?php

namespace App\Events;

use App\Enums\UserState;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserStateChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public User $user
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
        $chatsIds = $this->user->privateChats()->pluck('id');
        $users = User::whereHas('chats', function ($query) use ($chatsIds) {
            $query->whereIn('id', $chatsIds);
        })
            ->where('id', '<>', $this->user->id)
            ->where('state', '<>', UserState::OFFLINE)
            ->get();
        $channelsToBroadcastOn = [];
        foreach ($users as $activeUser) {
            $channelsToBroadcastOn[] = new PrivateChannel('chatUsers.' . $activeUser->id);
        }
        return $channelsToBroadcastOn;
    }
}
