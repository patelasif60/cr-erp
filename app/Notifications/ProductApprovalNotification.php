<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProductApprovalNotification extends Notification
{
    use Queueable;
    public $user, $note, $type, $url;

    public function __construct($user, $note, $type, $url)
    {
        $this->user = $user;
        $this->note = $note;
        $this->type = $type;
        $this->url = $url;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    
    public function toArray($notifiable)
    {
        return [
            'user_id' => $this->user['id'],
            'name' => $this->user['name'],
            'email' => $this->user['email'],
            'note' => $this->note,
            'type' => $this->type,
            'url' => $this->url,
        ];
    }
}
