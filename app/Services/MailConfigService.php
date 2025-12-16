<?php

namespace App\Services;

use App\Models\SmtpSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class MailConfigService
{
    public static function applyFromDb(): void
    {
        $s = SmtpSetting::latest()->first();

        if (!$s) {
            return; 
        }

        // Set runtime config
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

        // Reset connection to force new config usage
        Mail::purge('smtp');
    }

    public static function getReceivingEmail(): string
    {
        $s = SmtpSetting::latest()->first();
        return ($s && $s->receiving_email) ? $s->receiving_email : (config('mail.from.address') ?? env('MAIL_FROM_ADDRESS'));
    }
}