<?php


use App\Enums\SettingKey;
use App\Enums\Type;
use App\Models\Currency;
use App\Models\Setting;
use App\Models\User;
use App\Models\Voter;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;

if (!function_exists('admin')) {
    /**
     * returns authenticated admin user
     * @return User|Authenticatable|null
     */
    function admin(): User|Authenticatable|null
    {
        return auth('web')->user();
    }
}

if (!function_exists('logo')) {
    /**
     * returns site logo
     * @return string
     * @throws Throwable
     */
    function logo(): string
    {
        return setting(SettingKey::LOGO->value, true) ?? asset('assets/admin/images/logo/logo.png');
    }
}


if (!function_exists('message')) {
    /**
     * returns site message
     * @return string
     * @throws Throwable
     */
    function message(): string
    {
        return setting(SettingKey::MESSAGE->value, true).PHP_EOL ?? "";
    }
}
if (!function_exists('attendance')) {
    /**
     * returns site attended voters
     * @return string
     * @throws Throwable
     */
    function attendance(): string
    {
        return Voter::where('status', true)->count();
    }
}

if (!function_exists('voters')) {
    /**
     * returns site total voters
     * @return int
     * @throws Throwable
     */
    function voters(): int
    {
        return Voter::count();
    }
}


if (!function_exists('counts')) {
    /**
     * Returns the count of voters based on the normalized type.
     *
     * @param string $type
     * @return int
     */
    function counts($type): int
    {
        $Type = Type::normalize($type);

        return Voter::where('type', $Type)->count();
    }
}

if (!function_exists('setting')) {
    /**
     * Get setting by key
     * @param string $key
     * @param bool $parse
     * @return mixed|null
     * @throws Throwable
     */
    function setting(string $key, bool $parse = false): mixed
    {
        throw_if(!in_array($key, SettingKey::all()), new Exception('Invalid Setting Key!'));
        $options = Setting::key($key)->first()?->option_value;
        if ($parse) {
            return is_array($options) && !empty($options) ? $options[0] : '';
        }
        return $options ?? [];
    }

}
