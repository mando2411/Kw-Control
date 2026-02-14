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

    // Global UI policy: user_choice | modern | classic
    case UI_MODE_POLICY             = 'ui_mode_policy';

    // Modern UI Theme Tokens (Light)
    case UI_MODERN_BTN_PRIMARY      = 'ui_modern_btn_primary';
    case UI_MODERN_BTN_SECONDARY    = 'ui_modern_btn_secondary';
    case UI_MODERN_BTN_TERTIARY     = 'ui_modern_btn_tertiary';
    case UI_MODERN_BTN_QUATERNARY   = 'ui_modern_btn_quaternary';

    case UI_MODERN_TEXT_PRIMARY     = 'ui_modern_text_primary';
    case UI_MODERN_TEXT_SECONDARY   = 'ui_modern_text_secondary';

    case UI_MODERN_BG_PRIMARY       = 'ui_modern_bg_primary';
    case UI_MODERN_BG_SECONDARY     = 'ui_modern_bg_secondary';

    // Modern UI Theme Tokens (Dark)
    case UI_MODERN_DARK_BTN_PRIMARY    = 'ui_modern_dark_btn_primary';
    case UI_MODERN_DARK_BTN_SECONDARY  = 'ui_modern_dark_btn_secondary';
    case UI_MODERN_DARK_BTN_TERTIARY   = 'ui_modern_dark_btn_tertiary';
    case UI_MODERN_DARK_BTN_QUATERNARY = 'ui_modern_dark_btn_quaternary';

    case UI_MODERN_DARK_TEXT_PRIMARY   = 'ui_modern_dark_text_primary';
    case UI_MODERN_DARK_TEXT_SECONDARY = 'ui_modern_dark_text_secondary';

    case UI_MODERN_DARK_BG_PRIMARY     = 'ui_modern_dark_bg_primary';
    case UI_MODERN_DARK_BG_SECONDARY   = 'ui_modern_dark_bg_secondary';

    // Modern UI Typography Scale
    case UI_MODERN_FS_XS            = 'ui_modern_fs_xs';
    case UI_MODERN_FS_SM            = 'ui_modern_fs_sm';
    case UI_MODERN_FS_BASE          = 'ui_modern_fs_base';
    case UI_MODERN_FS_LG            = 'ui_modern_fs_lg';
    case UI_MODERN_FS_XL            = 'ui_modern_fs_xl';




    public static function all(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

}
