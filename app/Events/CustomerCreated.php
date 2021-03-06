<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CustomerCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $title;
    public string $message;
    public string $token;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user, $token)
    {
        $this->title = "Pesanan baru";
        $this->message  = "{$user} selesai melakukan pemesanan";
        $this->token = $token;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return string[]
     */
    public function broadcastOn(): array
    {
        return ['customer-created'];
    }

    public function broadcastAs(): string
    {
        return 'customer-created';
    }
}
