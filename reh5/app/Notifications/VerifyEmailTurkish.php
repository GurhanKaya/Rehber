<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailTurkish extends VerifyEmail
{
    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('E-posta Adresinizi Doğrulayın - Rehber Projesi 2025')
            ->greeting('Merhaba!')
            ->line('Rehber Projesi 2025 hesabınızı oluşturduğunuz için teşekkür ederiz.')
            ->line('E-posta adresinizi doğrulamak için aşağıdaki butona tıklayın:')
            ->action('E-posta Adresimi Doğrula', $verificationUrl)
            ->line('Bu e-posta 60 dakika içinde geçerliliğini yitirecektir.')
            ->line('Eğer hesap oluşturmadıysanız, bu e-postayı görmezden gelebilirsiniz.')
            ->salutation('Saygılarımızla, Rehber Projesi 2025 Ekibi');
    }
} 