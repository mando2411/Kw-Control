<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

use Illuminate\Foundation\Events\Dispatchable;

class VoteUpdated implements ShouldBroadcastNow
{
   
  use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $message;

    public function __construct(array $message)
  {
      $this->message = $message;
  }

    public function broadcastOn(): array
  {
      return [new Channel('votes')];
  }

    public function broadcastAs(): string
  {
      return 'my-event';
  }
}
