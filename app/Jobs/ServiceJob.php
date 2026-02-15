<?php

namespace App\Jobs;

use App\Enums\SettingKey;
use App\Mail\ServiceEmailMail;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ServiceJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function handle(): void
    {
        \Mail::to(setting(SettingKey::NOTIFICATION_EMAILS->value))->send(new ServiceEmailMail($this->data));
    }
}
