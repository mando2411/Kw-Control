<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class CommitteeUpdate implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $committee;
  
    public function __construct($committee)
    {
        $this->committee = $committee;
    }
  
    public function broadcastOn()
    {
        return ['committee'];
    }
  
    public function broadcastAs()
    {
        return 'event';
    }
}
