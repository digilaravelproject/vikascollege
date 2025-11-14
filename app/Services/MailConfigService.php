<?php

namespace App\Services;

use App\Models\SmtpSetting;
use Illuminate\Support\Facades\Config;

class MailConfigService
{
    /**
     * Apply SMTP settings from DB into runtime mail config
     */
    public static function applyFromDb(): void
    {
        $s = SmtpSetting::latest()->first();

        if (!$s) {

            return; // fallback to .env (which must not be 'log')
        }

        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.transport', 'smtp');
        Config::set('mail.mailers.smtp.host', $s->host);
        Config::set('mail.mailers.smtp.port', (int) $s->port);
        Config::set('mail.mailers.smtp.encryption', $s->encryption ?: null);
        Config::set('mail.mailers.smtp.username', $s->username);
        Config::set('mail.mailers.smtp.password', $s->password);

        if ($s->from_address) {
            Config::set('mail.from.address', $s->from_address);
            Config::set('mail.from.name', $s->from_name ?? config('app.name'));
        }
    }

    public static function getReceivingEmail(): string
    {
        $s = SmtpSetting::latest()->first();
        if ($s && $s->receiving_email) {
            return $s->receiving_email;
        }
        return config('mail.from.address') ?? env('MAIL_FROM_ADDRESS');
    }
}
