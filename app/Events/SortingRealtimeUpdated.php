<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SortingRealtimeUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $committeeId;
    public string $type;

    public function __construct(int $committeeId, string $type = 'metrics')
    {
        $this->committeeId = $committeeId;
        $this->type = $type;
    }

    public function broadcastOn(): array
    {
        return [new Channel('sorting.' . $this->committeeId)];
    }

    public function broadcastAs(): string
    {
        return 'sorting.realtime.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'committee_id' => $this->committeeId,
            'type' => $this->type,
            'ts' => now()->timestamp,
        ];
    }
}
