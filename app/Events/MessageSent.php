<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use App\Models\Message;

// app/Events/MessageSent.php

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message->load('sender');
    }

    public function broadcastOn()
    {
        if ($this->message->group_id) {
            return [new PrivateChannel('group.' . $this->message->group_id)];
        } else {
            $receiver = $this->message->receiver_id;
            $sender = $this->message->sender_id;
            $channel = 'private-chat.' . min($receiver, $sender) . '.' . max($receiver, $sender);
            return [new PrivateChannel($channel)];
        }
    }
    public function broadcastAs()
    {
        return 'message.sent';
    }
}

