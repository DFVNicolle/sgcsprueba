<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmail extends BaseVerifyEmail
{
    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verifica tu correo electrónico | SGCS')
            ->greeting('¡Bienvenido a SGCS!')
            ->line('Te damos la bienvenida al Sistema de Gestión de Configuración de Software (SGCS).')
            ->line('Para proteger tu cuenta y acceder a todas las funcionalidades, por favor verifica tu dirección de correo electrónico haciendo clic en el botón de abajo:')
            ->action('Verificar correo electrónico', $verificationUrl)
            ->line('Si tienes problemas con el botón, copia y pega el siguiente enlace en tu navegador:')
            ->line($verificationUrl)
            ->line('Si no creaste esta cuenta, puedes ignorar este mensaje.')
            ->salutation('Saludos cordiales,\nEquipo SGCS');
    }
}
