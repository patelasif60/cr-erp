<?php

namespace App\Jobs;

use App\CommunicationHistory;
use App\Repositories\CommunicationRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

use Log; 

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $email, $body;

    public function __construct($body, $email)
    {
        $this->email = $email;
        $this->body = $body;
    }

    public function handle()
    {
        try {
            Mail::to($this->email)->send($this->body);
        } catch (\Exception $e) {
            dump($e);
        }
    }

}