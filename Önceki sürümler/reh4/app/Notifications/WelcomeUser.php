<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class WelcomeUser extends Notification
{
    use Queueable;

    private $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Hesabınız Oluşturuldu - Hoş Geldiniz!')
            ->greeting('Merhaba ' . $this->user->name . '!')
            ->line('Hesabınız başarıyla oluşturuldu ve sisteme erişim izniniz verildi.')
            ->line('E-posta adresiniz: ' . $this->user->email)
            ->line('Şifrenizi öğrenmek için lütfen sistem yöneticisi ile iletişime geçiniz.')
            ->line('İlk giriş yaptığınızda şifrenizi değiştirmenizi öneririz.')
            ->action('Giriş Yap', url('/login'))
            ->line('Herhangi bir sorunuz varsa sistem yöneticisi ile iletişime geçebilirsiniz.');
    }
} 