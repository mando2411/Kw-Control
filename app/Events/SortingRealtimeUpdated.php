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
    public int $electionId;

    public function __construct(int $committeeId, string $type = 'metrics', int $electionId = 0)
    {
        $this->committeeId = $committeeId;
        $this->type = $type;
        $this->electionId = $electionId;
    }

    public function broadcastOn(): array
    {
        $channels = [new Channel('sorting.' . $this->committeeId)];

        if ($this->electionId > 0) {
            $channels[] = new Channel('results.' . $this->electionId);
        }

        return $channels;
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
            'election_id' => $this->electionId,
            'ts' => now()->timestamp,
        ];
    }
}
