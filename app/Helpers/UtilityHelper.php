<?php

use App\Models\Notification;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request as facadesRequest;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

/**
 * Function used to generate otp
 *
 * @return number
 */
function generateOtp()
{
    $digits = config('constants.otp.otp_length');
    if (config('constants.otp.is_default')) {
        return config('constants.otp.default');
    }

    return rand(pow(10, $digits - 1), pow(10, $digits) - 1);
}

/**
 * Method checkAppVersion
 *
 * @param Request $request [explicite description]
 *
 * @return array
 */
function checkAppVersion(Request $request)
{
    $response = [
        'success' => true,
    ];

    $appVersion = $request->header('app-version');
    $deviceType = $request->header('device-type');
    if (! empty($deviceType) && ! empty($appVersion)) {
        if ('ios' == $deviceType) {
            $data = DB::table('settings')
                ->where('setting_key', 'ios_app_version')
                ->first();
            if ($data && version_compare($appVersion, $data->setting_value, '<')) {
                $response = [
                    'success' => false,
                    'data' => [],
                    'message' => 'There is a newer version available for download! Please update the app by visiting the App store.',
                    'url' => 'https://itunes.apple.com/app/',
                ];
            }
        } elseif ('android' == $deviceType) {
            $data = DB::table('settings')
                ->where('setting_key', 'android_app_version')
                ->first();
            if ($data && version_compare($appVersion, $data->setting_value, '<')) {
                $response = [
                    'success' => false,
                    'data' => [],
                    'message' => 'There is a newer version available for download! Please update the app by visiting the Play store.',
                ];
            }
        }
    }

    return $response;
}

/**
 * Method sendMail
 *
 * @param $to       $to [explicite description]
 * @param $template 'Illuminate\Contracts\Mail\Mailable' [explicite description]
 *
 * @return void
 */
function sendMail($to, $template): void
{
    Mail::to($to)->send($template);
}

/**
 * Method changeDateToFormat
 *
 * @param $date   string [explicite description]
 * @param $format $format [explicite description]
 *
 * @return void
 */
function changeDateToFormat($date, $format = '')
{
    $format = ! empty($format) ? $format : 'Y-m-d';

    return date($format, strtotime($date));
}

/**
 * Method currentDateByFormat
 *
 * @param $format string [explicite description]
 *
 * @return void
 */
function currentDateByFormat($format)
{
    return date($format);
}

/**
 * Method convertMinutesToHours
 *
 * @param int $minutes [explicite description]
 *
 * @return void
 */
function convertMinutesToHours(int $minutes)
{
    $hours = floor($minutes / 60);
    $min = $minutes - ($hours * 60);

    if (! $min) {
        return $hours;
    }

    return $hours . ':' . $min;
}

/**
 * Method generateReferralCode
 *
 * @param int $length [explicite description]
 *
 * @return string
 */
function generateReferralCode(int $length = 8): string
{
    $str_result = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz';

    return substr(str_shuffle($str_result), 0, $length);
}

/**
 * Method obfuscateEmail
 *
 * @param $email string [explicite description]
 *
 * @return void
 */
function obfuscateEmail($email)
{
    $em = explode('@', $email);
    $name = implode('@', array_slice($em, 0, count($em) - 1));
    $len = floor(strlen($name) / 2);

    return substr($name, 0, $len) . str_repeat('*', $len) . '@' . end($em);
}



/**
 * Method pageLoader
 *
 * @return void
 */
function pageLoader(): void
{
    echo '<div class="pageLoader text-center"><div class="spinner-border" role="status"></div></div>';
}

/**
 * Get duration in hours and min
 *
 * @param int $minutes [explicite description]
 *
 * @return string
 */
function getDuration($minutes)
{
    $text = '';
    if ($minutes >= 60) {
        $hours = (int) ($minutes / 60);
        $minutes = $minutes % 60;
        if ($hours) {
            $text .= $hours . ' ' . trans('labels.h');
        }

        if ($minutes) {
            $text .= ' ' . $minutes . ' ' . trans('labels.min');
        }
    } else {
        $text = $minutes . ' ' . trans('labels.min');
    }

    return $text;
}

/**
 * Method convertDateToTz
 *
 * @param $date             string [explicite description]
 * @param $fromTz           $fromTz [explicite description]
 * @param $format           $format [explicite description]
 * @param $toTz             $toTz [explicite description]
 * @param $withoutTranslate $withoutTranslate [explicite description]
 *
 * @return void
 */
function convertDateToTz(
    $date,
    $fromTz = '',
    $format = 'Y-m-d H:i:s',
    $toTz = '',
    $withoutTranslate = ''
) {
    if ('' == $toTz && (Session::get('timezone'))) {
        $toTz = Session::get('timezone');
    } elseif ('' == $toTz) {
        $toTz = config('app.timezone');
    }

    if (! $fromTz) {
        $fromTz = config('app.timezone');
    }
    $date = new \DateTime($date, new \DateTimeZone($fromTz));
    $date->setTimezone(new \DateTimeZone($toTz));
    $date = $date->format($format);
    if ($withoutTranslate) {
        return $date;
    }

    return Carbon::parse($date)->translatedFormat($format);
}

/**
 * Get current date time
 *
 * @param $format   [explicite description]
 * @param $timezone [explicite description]
 *
 * @return string
 */
function nowDate($format = 'Y-m-d H:i:s', $timezone = null)
{
    $timezone = $timezone ? $timezone : Session::get('timezone');

    return Carbon::now()->setTimezone($timezone)->format($format);
}



/**
 * Method makeSlug
 *
 * @param string $string      [explicite description]
 * @param $model       $model [explicite description]
 * @param $key         $key [explicite description]
 * @param $separator   $separator [explicite description]
 * @param $withTrashed $withTrashed [explicite description]
 *
 * @return void
 */
function makeSlug(
    string $string,
    $model = null,
    $key = '',
    $separator = '-',
    $withTrashed = false
) {
    $slug = Str::slug($string, $separator);
    $query = $model::whereSlug($slug);
    if ($withTrashed) {
        $query->withTrashed();
    }
    $checkExists = $query->exists();
    if ($model && $key && $checkExists) {
        $qry = $model::where('slug', 'Like', '%' . $slug . '%');
        if ($withTrashed) {
            $qry->withTrashed();
        }
        $max = $qry->count();
        if ($max) {
            $max = $max + 1;
            $slug = "{$slug}-{$max}";
        }
    }

    return $slug;
}

/**
 * Method expiryDays
 *
 * @param $date $date [explicite description]
 *
 * @return void
 */
function expiryDays($date)
{
    $date = Carbon::parse($date);
    $now = Carbon::now();
    $day = $date->diffInDays($now);
    $return = '';
    if (0 == $day) {
        $return = trans('labels.today_expiry');
    } elseif (1 == $day) {
        $return = $day . ' ' . trans('labels.day');
    } else {
        $return = $day . ' ' . trans('labels.days');
    }

    return $return;
}

if (! function_exists('asset_path')) {
    /**
     * Get the path to a versioned Mix file.
     *
     * @param $path              string
     * @param $manifestDirectory string
     *
     * @return \Illuminate\Support\HtmlString|string
     *
     * @throws \Exception
     */
    function asset_path($path, $manifestDirectory = '')
    {
        $mixPath = asset(mix($path, $manifestDirectory));

        return $mixPath;
    }
}

/**
 * Method get sidebar route check.
 *
 * @param string $name = ''
 *
 * @return string / emptystring
 */
function sidebarRouteCheck(string $name = '')
{
    return ((('' != $name) && (facadesRequest::routeIs($name)))
        ? 'active current-page' : facadesRequest::is($name)) ? 'active current-page' : '';
}

/**
 * Get loggedin user ID
 *
 * @return User/bool
 */
function getLoggedInUserDetail()
{
    if (auth()->check()) {
        return auth()->user();
    }

    return false;
}

/**
 * Method returnScriptWithNonce
 *
 * @param $path $path [explicite description]
 *
 * @return void
 */
function returnScriptWithNonce($path)
{
    return '<script nonce="' . csp_nonce('script') . '" src="' . $path . '"> </script>';
}

/**
 * Method getConvertedDate
 *
 * @param $date   string
 * @param $format string
 *
 * @return string
 */
function getConvertedDate($date, $format = '')
{
    if ('' == $format) {
        $format = config('constants.date_format.admin_display');
    }

    return Carbon::parse($date)->format($format);
}

/**
 * Method getAppName
 *
 * @return string
 */
function getAppName()
{
    return config('app.name');
}

/**
 * Method getShortName
 *
 * @param $string string [explicite description]
 *
 * @return void
 */
function getShortName($string)
{
    $words = explode(' ', $string);
    $acronym = '';

    foreach ($words as $w) {
        $acronym .= mb_substr($w, 0, 1);
    }

    return $acronym;
}

