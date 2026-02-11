<?php

namespace App\Enums;

enum SettingKey: string
{
    case SOCIAL_LINKS               = 'social_links';
    case NOTIFICATION_EMAILS        = 'notification_emails';
    case SITE_TITLE                 = 'site_title';
    case LOGO                       = 'logo';
    case WHATSAPP_NUMBER            = 'whatsapp_number';
    case CONTACT_EMAIL              = 'contact_email';
    case ADDRESS                    = 'address';
    case PRIMARY_PHONE              = 'primary_phone';
    case LOCATION                   = 'location';
    case PHONE_NUMBERS              = 'phone-numbers';
    case GALLERY                    = 'gallery';
    case BANNER_GALLERY             = 'banner_gallery';
    case MESSAGE                    = 'message';
    case RESULT_CONTROL             = 'result_control';
    case RESULT_CONTROL_CANDIDATE   = 'result_control_candidate';




    public static function all(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

}
