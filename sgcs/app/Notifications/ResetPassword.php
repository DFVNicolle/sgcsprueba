<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPassword extends BaseResetPassword
{
    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        return (new MailMessage)
            ->subject('Restablecer contraseña | SGCS')
            ->greeting('¡Hola!')
            ->line('Has recibido este correo porque se solicitó un restablecimiento de contraseña para tu cuenta en el Sistema de Gestión de Configuración de Software (SGCS).')
            ->action('Restablecer contraseña', url(config('app.url').route('password.reset', ['token' => $this->token, 'email' => $notifiable->getEmailForPasswordReset()], false)))
            ->line('Este enlace de restablecimiento expirará en :count minutos.', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')])
            ->line('Si no solicitaste un restablecimiento de contraseña, puedes ignorar este correo.')
            ->salutation('Saludos cordiales,\nEquipo SGCS');
    }
}
