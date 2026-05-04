<?php
// app/Notifications/NewMessageNotification.php
namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMessageNotification extends Notification
{
    use Queueable;

    protected $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Nouveau message reçu')
            ->line('Vous avez reçu un nouveau message concernant: ' . $this->message->subject)
            ->action('Voir le message', route('messages.show', $this->message))
            ->line('Merci d\'utiliser notre plateforme!');
    }

    public function toArray($notifiable)
    {
        return [
            'message_id' => $this->message->id,
            'subject' => $this->message->subject,
            'sender_name' => $this->message->sender->name
        ];
    }
}
