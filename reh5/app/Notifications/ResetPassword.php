<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPassword extends BaseResetPassword
{
    /**
     * Build the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Şifre Sıfırlama')
            ->greeting('Merhaba!')
            ->line('Bu e-postayı, hesabınız için şifre sıfırlama isteği aldığımız için alıyorsunuz.')
            ->action('Şifreyi Sıfırla', $url)
            ->line('Bu şifre sıfırlama bağlantısı 60 dakika içinde sona erecektir.')
            ->line('Şifre sıfırlama isteğinde bulunmadıysanız, başka bir işlem yapmanıza gerek yoktur.')
            ->salutation('Saygılarımızla, Reh3 Ekibi');
    }
}
