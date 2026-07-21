<?php

use Hashids\Hashids;

if (!function_exists('versioned_asset')) {
    /**
     * Returns the URL for an asset appended with its last modified time for cache busting.
     *
     * @param string $path Relative path to the asset (e.g., 'assets/js/custom.js')
     * @return string Versioned URL
     */
    function versioned_asset(string $path): string
    {
        // Get the absolute path on the server
        $absolutePath = ROOTPATH . 'public/' . $path;  // Adjust depending on your directory structure
        if (file_exists($absolutePath)) {
            // Append modification time as query parameter
            // debug(filemtime($absolutePath));
            // die("test");
            return base_url($path) . '?v=' . filemtime($absolutePath);
        }
        // Fallback if file doesn't exist
        return base_url($path);
    }
}

if (!function_exists('debug')) {
    /**
     * Returns the base URL for the application.
     *
     * @param string $path Optional path to append to the base URL
     * @return string Base URL
     */
    function debug($data): void
    {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }
}

function setKey($data, $module = "")
{
    return encryptData($data, $module);
}

function getKey($data, $module = "")
{
    if (empty($data) || $data === '0') {
        return null;
    }

    return decryptData($data, $module);
}

function encryptData($data, $namespace = 'global')
{
    $baseKey = getenv('encryptionKey') ?: 'myEncryptionKey';
    $key = hash('sha256', $baseKey . '::' . $namespace);

    $cipher = "aes-256-cbc";
    $ivlen = openssl_cipher_iv_length($cipher);
    $iv = openssl_random_pseudo_bytes($ivlen);

    $encrypted = openssl_encrypt($data, $cipher, $key, 0, $iv);
    return rtrim(strtr(base64_encode($encrypted . '::' . base64_encode($iv)), '+/=', '-_~'), '=');
}

function decryptData($encryptedInput, $namespace = 'global')
{
    if (empty($encryptedInput) or $encryptedInput === '0') {
        return null;
    }

    $baseKey = getenv('encryptionKey') ?: 'myEncryptionKey';
    $key = hash('sha256', $baseKey . '::' . $namespace);

    $cipher = "aes-256-cbc";

    $decoded = base64_decode(strtr($encryptedInput, '-_~', '+/='));
    if (!$decoded || strpos($decoded, '::') === false) {
        return null;
    }

    list($encrypted, $iv) = explode('::', $decoded, 2);

    $iv = base64_decode($iv);
    if ($iv === false) {
        return null;
    }

    $decrypted = openssl_decrypt($encrypted, $cipher, $key, 0, $iv);
    return $decrypted !== false ? $decrypted : null;
}


// Old Function
// function getLogoUrl()
// {
//     $appLogo = '/assets/img/appLogo.png';
//     $defaultLogo = '/assets/img/defaultLogo.png';

//     if (file_exists($appLogo)) {
//         return base_url($appLogo);
//     } else {
//         return base_url($defaultLogo);
//     }
// }

// New Function
function getLogoUrl($type = 'dark')
{
    if ($type == 'dark') {
        $defaultLogo = '/assets/img/darkLogo.png';
        $appLogo = '/uploads/branding/1/darkAppLogo.png';
    } else if ($type == 'light') {
        $defaultLogo = '/assets/img/lightLogo.png';
        $appLogo = '/uploads/branding/1/lightAppLogo.png';
    } else if ($type == 'print') {
        $defaultLogo = '/assets/img/printLogo.png';
        $appLogo = '/uploads/branding/1/printAppLogo.png';
    } else {
        $defaultLogo = '/assets/img/defaultLogo.png';
        $appLogo = '';
    }

    // Check if both logos are missing
    if ((empty($defaultLogo) || !file_exists(FCPATH . ltrim($defaultLogo, '/'))) &&
        (empty($appLogo) || !file_exists(FCPATH . ltrim($appLogo, '/')))
    ) {
        $defaultLogo = '/assets/img/defaultLogo.png';
        $appLogo = '';
    }

    $appLogoPath = FCPATH . ltrim($appLogo, '/');

    if (!empty($appLogo) && file_exists($appLogoPath)) {
        return base_url($appLogo);
    } else {
        return base_url($defaultLogo);
    }
}

function getLogoPath()
{
    $appLogo = FCPATH . '/assets/img/appLogo.png';
    $defaultLogo = FCPATH . '/assets/img/defaultLogo.png';

    if (file_exists($appLogo)) {
        return $appLogo;
    } else {
        return $defaultLogo;
    }
}

function getFaviconShortUrl()
{
    $favicon = getFaviconUrl();

    $path = str_replace(base_url(), "", $favicon);
    $dir = FCPATH . dirname($path);

    $smallPath = $dir . "/favicon192x192.png";
    $fullPath = FCPATH . $path;

    if (!file_exists($smallPath)) {
        $image = new \Gumlet\ImageResize($fullPath);
        $image->resizeToShortSide(192);
        $image->save($smallPath);
    }

    return base_url(str_replace(FCPATH, "", $smallPath));
}

function getFaviconUrl()
{
    $favicon = '/uploads/branding/1/appFavicon.png';
    $defaultFavicon = '/assets/img/defaultFavicon.png';

    $faviconLogoPath = FCPATH . ltrim($favicon, '/');

    if (!empty($favicon) && file_exists($faviconLogoPath)) {
        return base_url($favicon);
    } else {
        return base_url($defaultFavicon);
    }
}

function getFaviconPath()
{
    $favicon = FCPATH . '/assets/img/favicon.png';
    $defaultFavicon = FCPATH . '/assets/img/defaultFavicon.png';

    if (file_exists($favicon)) {
        return $favicon;
    } else {
        return $defaultFavicon;
    }
}

function getLoginBgUrl()
{
    $loginBg = '/uploads/branding/1/appLoginBg.jpg';
    $defaultLoginBg = '/assets/img/defaultLoginBg.jpg';

    $loginBgPath = FCPATH . ltrim($loginBg, '/');

    if (!empty($loginBg) && file_exists($loginBgPath)) {
        return base_url($loginBg);
    } else {
        return base_url($defaultLoginBg);
    }
}

function getLoginBgPath()
{
    $loginBg = FCPATH . '/assets/img/appLoginBg.jpg';
    $defaultLoginBg = FCPATH . '/assets/img/defaultLoginBg.jpg';

    if (file_exists($loginBg)) {
        return $loginBg;
    } else {
        return $defaultLoginBg;
    }
}


function isActiveMenu($menuItem, $currentUri, $lazyMatch = false)
{
    $menuUrl = str_replace(base_url(), "", $menuItem->url);
    // $currentUri = str_replace(base_url(), "", $currentUri);

    // $menuUrl = $menuItem->url;
    // $currentUri = $currentUri;

    if ($lazyMatch) {
        // compare both addUser and editUser as same.
        $currentUri = str_replace("edit", "add", $currentUri);
        $matchWith = str_replace("edit", "add", $menuUrl);

        if ($matchWith == $currentUri) {
            return true;
        } else {

            // keep only first two segments of the URL
            $currentUri = implode('/', array_slice(explode('/', $currentUri), 0, 2));
            $matchWith = implode('/', array_slice(explode('/', $menuUrl), 0, 2));

            if ($matchWith == $currentUri) {
                return true;
            }
        }
    } else {
        if ($menuUrl == $currentUri) {
            return true;
        }
    }



    if (!empty($menuItem->children)) {
        foreach ($menuItem->children as $child) {
            if (isActiveMenu($child, $currentUri)) {
                return true;
            }
        }

        $parentFound = false;
        foreach ($menuItem->children as $child) {
            if (isActiveMenu($child, $currentUri, true)) {
                $parentFound = true;
                break;
            }
        }

        if ($parentFound) {
            return true;
        }
    }

    return false;
}


function timenow()
{
    return date("Y-m-d H:i:s");
}


define("HOUR_IN_SECONDS", (60 * 60));
define("DAY_IN_SECONDS", (60 * 60 * 24));
define("WEEK_IN_SECONDS", (60 * 60 * 24 * 7));
define("YEAR_IN_SECONDS", (60 * 60 * 24 * 365));
define("MINUTE_IN_SECONDS", (60));

function humanTimeDifference($from, $to = '')
{
    if (is_null($from)) {
        return "N/A";
    }

    if (!is_numeric($from)) {
        $from = strtotime($from);
    }

    if (!is_numeric($to)) {
        $to = strtotime($to);
    }


    if (empty($to))
        $to = time();

    $diff = (int) abs($to - $from);

    if ($diff < HOUR_IN_SECONDS) {
        $mins = round($diff / MINUTE_IN_SECONDS);
        if ($mins <= 1)
            $mins = 1;
        /* translators: min=minute */
        $since = sprintf(_n('%s min', '%s mins', $mins), $mins);
    } elseif ($diff < DAY_IN_SECONDS && $diff >= HOUR_IN_SECONDS) {
        $hours = round($diff / HOUR_IN_SECONDS);
        if ($hours <= 1)
            $hours = 1;
        $since = sprintf(_n('%s hour', '%s hours', $hours), $hours);
    } elseif ($diff < WEEK_IN_SECONDS && $diff >= DAY_IN_SECONDS) {
        $days = round($diff / DAY_IN_SECONDS);
        if ($days <= 1)
            $days = 1;
        $since = sprintf(_n('%s day', '%s days', $days), $days);
    } elseif ($diff < 30 * DAY_IN_SECONDS && $diff >= WEEK_IN_SECONDS) {
        $weeks = round($diff / WEEK_IN_SECONDS);
        if ($weeks <= 1)
            $weeks = 1;
        $since = sprintf(_n('%s week', '%s weeks', $weeks), $weeks);
    } elseif ($diff < YEAR_IN_SECONDS && $diff >= 30 * DAY_IN_SECONDS) {
        $months = round($diff / (30 * DAY_IN_SECONDS));
        if ($months <= 1)
            $months = 1;
        $since = sprintf(_n('%s month', '%s months', $months), $months);
    } elseif ($diff >= YEAR_IN_SECONDS) {
        $years = round($diff / YEAR_IN_SECONDS);
        if ($years <= 1)
            $years = 1;
        $since = sprintf(_n('%s year', '%s years', $years), $years);
    }

    return $since;
}

function _n($single, $plural, $number, $domain = 'default')
{
    if ($number == 1)
        return $single;
    return $plural;
}


//date and time format set
function dateFormats()
{
    return [
        'd/m/Y' => date('d/m/Y'), //08/02/2025
        'd-m-Y' => date('d-m-Y'), //08-02-2025
        'd.M.Y' => date('d.M.Y'), //08.Feb.2025
        'j M Y' => date('j M Y'), //8 Feb 2025
        'j F Y' => date('j F Y'), //8 February 2025
        'D, d M Y' => date('D, d M Y'), //Mon, 08 Feb 2025
        'l, d F Y' => date('l, d F Y'), //Monday, 08 February 2025
        'Y-m-d' => date('Y-m-d'), //2025-02-08
        'm/d/Y' => date('m/d/Y'), //02/08/2025
    ];
}
function timeFormats()
{
    return [
        'g:i A' => date('g:i A'), // 12-hour format without leading zeros, AM/PM
        'h:i A' => date('h:i A'), // 12-hour format with leading zeros, AM/PM
        'G:i' => date('G:i'), // 24-hour format without leading zeros
        'H:i' => date('H:i'), // 24-hour format with leading zeros
        'g:i:s A' => date('g:i:s A'), // 12-hour format with seconds, AM/PM
        'h:i:s A' => date('h:i:s A'), // 12-hour format with leading zeros and seconds, AM/PM
        'G:i:s' => date('G:i:s'), // 24-hour format without leading zeros, with seconds
        'H:i:s' => date('H:i:s'), // 24-hour format with leading zeros and seconds
        'g:i A T' => date('g:i A T'), // 12-hour format with timezone abbreviation
        'h:i A T' => date('h:i A T'), // 12-hour format with leading zeros and timezone abbreviation
        'G:i T' => date('G:i T'), // 24-hour format without leading zeros and timezone abbreviation
        'H:i T' => date('H:i T'), // 24-hour format with leading zeros and timezone abbreviation
    ];
}

function dateTimeFormats()
{
    $formats = [];
    $dateFormats = dateFormats();
    $timeFormats = timeFormats();

    foreach ($dateFormats as $dateKey => $dateValue) {
        foreach ($timeFormats as $timeKey => $timeValue) {
            // $formats[$dateKey . ' ' . $timeKey] = $dateValue . ' ' . $timeValue;
            $formats[$dateKey . ', ' . $timeKey] = $dateValue . ', ' . $timeValue;
            // $formats[$timeKey . ' ' . $dateKey] = $timeValue . ' ' . $dateValue;
            // $formats[$timeKey . ', ' . $dateKey] = $timeValue . ', ' . $dateValue;
        }
    }

    return $formats;
}


function myDateFormat($date)
{
    if (empty($date)) {
        return null;
    }
    $config = config("AppConfig");
    return date($config->dateFormat, strtotime($date));
}

function myTimeFormat($time)
{
    if (empty($time)) {
        return null;
    }
    $config = config("AppConfig");
    return date($config->timeFormat, strtotime($time));
}

function myDateTimeFormat($datetime)
{
    if (empty($datetime)) {
        return null;
    }
    $config = config("AppConfig");
    return date($config->dateTimeFormat, strtotime($datetime));
}


function printable($str)
{
    $str = (string) @$str;

    // Insert spaces before capital letters in camelCase, but only if the previous character is an alphabet
    $str = preg_replace('/(?<=[a-z])(?=[A-Z])/', ' ', $str);
    $str = str_replace("_", " ", $str);

    // Split the string into words and process each word
    $words = explode(' ', $str);

    foreach ($words as &$word) {
        // If the word is all uppercase, leave it as it is
        if (mb_strtoupper($word) === $word) {
            continue;
        } else {
            // Otherwise, capitalize the first letter
            $word = ucfirst(strtolower($word));
        }
    }

    // Join the processed words back into a string
    return implode(' ', $words);
}

function username($userId = null)
{
    //    return $user_id;
    if (!is_null($userId))
        $user_id = (int) $userId;

    if ($userId === 0)
        return "System";

    $db = \Config\Database::connect();

    $user = $db->table('userMaster')->where('userId', $userId)->get()->getRow();

    if ($user) {
        return ucwords(strtolower($user->firstName . " " . $user->lastName));
    } else {
        return "Unknown User";
    }
}

function userNameInitial($userId = null)
{
    if (!is_null($userId))
        $user_id = (int) $userId;

    if ($userId === 0)
        return "-";

    $db = \Config\Database::connect();

    $user = $db->table('userMaster')->where('userId', $userId)->get()->getRow();

    if ($user) {
        return strtoupper($user->firstName[0] . $user->lastName[0]);
    } else {
        return "-";
    }
}

function time_diff($time1, $time2, $precision = 2)
{
    // If not numeric then convert timestamps
    if (!is_int($time1)) {
        $time1 = strtotime($time1);
    }
    if ($time2 == "") {
        $time2 = time();
    } else if (!is_int($time2)) {
        $time2 = strtotime($time2);
    }
    // If time1 > time2 then swap the 2 values
    if ($time1 > $time2) {
        list($time1, $time2) = array($time2, $time1);
    }

    // Set up intervals and diffs arrays
    $intervals = array('year', 'month', 'day', 'hour', 'minute', 'second');
    $diffs = array();
    foreach ($intervals as $interval) {
        // Create temp time from time1 and interval
        $ttime = strtotime('+1 ' . $interval, $time1);
        // Set initial values
        $add = 1;
        $looped = 0;
        // Loop until temp time is smaller than time2
        while ($time2 >= $ttime) {
            // Create new temp time from time1 and interval
            $add++;
            $ttime = strtotime("+" . $add . " " . $interval, $time1);
            $looped++;
        }
        $time1 = strtotime("+" . $looped . " " . $interval, $time1);
        $diffs[$interval] = $looped;
    }
    $count = 0;
    $times = array();
    foreach ($diffs as $interval => $value) {
        // Break if we have needed precission
        if ($count >= $precision) {
            break;
        }
        // Add value and interval if value is bigger than 0
        if ($value > 0) {
            if ($value != 1) {
                $interval .= "s";
            }
            // Add value and interval to times array
            $times[] = $value . " " . $interval;
            $count++;
        }
    }
    // Return string with times
    return implode(", ", $times);
}

function otp()
{
    return rand(111111, 999999);
}

function password($length = 8)
{
    $alphabet = 'abcdefghjklmnpqrtuvwxyABCDEFGHIJKLMNPQRSTUVWXY123456789';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < $length; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

function base64ToImage($base64String, $outputFile)
{
    //extract directory path from output file
    $dir = pathinfo($outputFile, PATHINFO_DIRNAME);

    //create directory if not exists
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }


    // open the output file for writing
    $ifp = fopen($outputFile, 'wb');

    // split the string on commas
    // $data[ 0 ] == "data:image/png;base64"
    // $data[ 1 ] == <actual base64 string>
    $data = explode(',', $base64String);

    // we could add validation here with ensuring count( $data ) > 1
    fwrite($ifp, base64_decode($data[1]));

    // clean up the file resource
    fclose($ifp);

    return $outputFile;
}

function imageToBase64($path)
{
    $type = pathinfo($path, PATHINFO_EXTENSION);
    $data = file_get_contents($path);
    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    return $base64;
}

function userProfilePicUrl($userId)
{
    if (file_exists(FCPATH . "uploads/users/photo/" . md5($userId) . ".png")) {
        return base_url("uploads/users/photo/" . md5($userId) . ".png");
    }
    return base_url("assets/img/user.png");
}

function userProfilePicPath($userId)
{
    if (file_exists(FCPATH . "uploads/users/photo/" . md5($userId) . ".png")) {
        return FCPATH . "uploads/users/photo/" . md5($userId) . ".png";
    }
    return FCPATH . "assets/img/user.png";
}


function formatCurrency($amount, $currencyCode = 'INR', $locale = 'en_IN', $decimalPoints = 2)
{
    $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
    $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $decimalPoints);
    return $formatter->formatCurrency($amount, $currencyCode);
}

function formatNumber($number, $locale = 'en_IN', $decimalPoints = 2)
{
    $formatter = new NumberFormatter($locale, NumberFormatter::DECIMAL);
    $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $decimalPoints);
    return $formatter->format($number);
}

function formatCurrencyWords($amount, $currencyCode = 'INR', $locale = 'en_IN')
{
    // Initialize spellout formatter for the given locale
    $formatter = new NumberFormatter($locale, NumberFormatter::SPELLOUT);

    // Ensure amount has two decimal places and split into whole and fractional parts
    $formattedAmount = number_format($amount, 2, '.', '');
    list($wholePart, $fractionPart) = explode('.', $formattedAmount);

    // Convert numeric parts to words
    $wordsWhole = $formatter->format($wholePart);
    $wordsFraction = $formatter->format($fractionPart);

    // Define currency unit names mapping
    $currencyNames = [
        'USD' => ['dollar', 'cent'],
        'INR' => ['rupee', 'paise'],
        'GBP' => ['pound', 'pence'],
        // Add more currencies as needed
    ];

    // Use plural form if needed based on amount (basic check; can be refined)
    $names = $currencyNames[$currencyCode] ?? ['unit', 'subunit'];
    $mainUnit = (intval($wholePart) === 1) ? $names[0] : $names[0] . 's';
    $subUnit  = (intval($fractionPart) === 1) ? $names[1] : $names[1] . 's';

    // Construct and return the final string
    return trim("{$wordsWhole} {$mainUnit} and {$wordsFraction} {$subUnit}");
}


function slugify($text, string $divider = '-')
{
    // replace non letter or digits by divider
    $text = preg_replace('~[^\pL\d]+~u', $divider, $text);

    // transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);

    // trim
    $text = trim($text, $divider);

    // remove duplicate divider
    $text = preg_replace('~-+~', $divider, $text);

    // lowercase
    $text = strtolower($text);

    if (empty($text)) {
        return 'n-a';
    }

    return $text;
}


function getFinancialYear($inputDate = null)
{
    // Use provided date or current date if null
    $date = $inputDate ? new DateTime($inputDate) : new DateTime();
    $year = (int)$date->format('Y');
    $month = (int)$date->format('n');

    // Indian Financial Year: April 1 to March 31
    if ($month < 4) {
        // Before April: belongs to previous FY
        $start = new DateTime(($year - 1) . '-04-01');
        $end   = new DateTime($year . '-03-31');
    } else {
        // April or later: current FY
        $start = new DateTime($year . '-04-01 00:00:00');
        $end   = new DateTime(($year + 1) . '-03-31 23:59:59');
    }

    // Format output as needed
    return (object) [
        'start' => $start->format('Y-m-d H:i:s'),
        'end'   => $end->format('Y-m-d H:i:s'),
        'fy'    => $start->format('Y') . '-' . $end->format('Y')
    ];
}

function popoverText($text, $charLength = 25, $wordLength = 3)
{
    // Split the text by spaces
    $plainText = strip_tags($text);
    // debug($plainText);
    // die;
    $words = explode(' ', $plainText);

    if (count($words) == 1) {
        // If there's only one word, check its length
        $word = $words[0];
        if (strlen($word) > $charLength) {
            $truncatedWord = substr($word, 0, $charLength) . '...';
            $remarks = '<span type="button" class="popover_btn" data-bs-html="true" data-bs-trigger="focus" data-container="body" data-bs-toggle="popover" data-bs-placement="top" data-bs-content="' . htmlspecialchars($word) . '">' . htmlspecialchars($truncatedWord) . '</span>';
        } else {
            $remarks = htmlspecialchars($word);
        }
    } else {
        // If there are multiple words, truncate and create the popover
        $truncatedText = implode(' ', array_slice($words, 0, $wordLength));
        $remarks = '<span type="button" class="popover_btn" data-bs-html="true" data-bs-trigger="focus" data-container="body" data-bs-toggle="popover" data-bs-placement="top" data-bs-content="' . nl2br(htmlspecialchars($text)) . '">' . htmlspecialchars($truncatedText) . '...</span>';
    }

    return $remarks;

    // if (is_null($text))
    //     $text = "";
    // $tr = implode(' ', array_slice(explode(' ', $text), 0, 3));
    // //$remarks = '<span type = "button" class = "popover_btn" title = "' . nl2br($text) . '" >' . $tr . '...</span>';
    // $remarks = '<span type="button" class="popover_btn" data-bs-html="true" data-bs-trigger="focus" data-container="body" data-bs-toggle="popover" data-bs-placement="top" data-bs-content="' . nl2br($text) . '">' . $tr . '...</span>';
}

function dateFilterOptions($type = "past")
{
    $pastList = [
        'today' => 'Today',
        'yesterday' => 'Yesterday',
        'this_week' => 'This Week',
        'last_week' => 'Last Week',
        'this_month' => 'This Month',
        'last_month' => 'Last Month',
        'this_year' => 'This Year',
        'last_year' => 'Last Year',
        'past_all' => 'Past All',
    ];

    $futureList = [
        'today' => 'Today',
        'tomorrow' => 'Tomorrow',
        'this_week' => 'This Week',
        'next_week' => 'Next Week',
        'this_month' => 'This Month',
        'next_month' => 'Next Month',
        'this_year' => 'This Year',
        'next_year' => 'Next Year',
        'future_all' => 'Future All',
    ];

    if ($type == "past") {
        return $pastList;
    } else if ($type == "future") {
        return $futureList;
    } else {
        return array_merge($pastList, $futureList);
    }
}

function dateFilterOptionRange($option)
{
    $range = [];
    $today = date('Y-m-d');
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    $tomorrow = date('Y-m-d', strtotime('+1 day'));
    $thisWeekStart = date('Y-m-d', strtotime('monday this week'));
    $thisWeekEnd = date('Y-m-d', strtotime('sunday this week'));
    $lastWeekStart = date('Y-m-d', strtotime('monday last week'));
    $lastWeekEnd = date('Y-m-d', strtotime('sunday last week'));
    $nextWeekStart = date('Y-m-d', strtotime('monday next week'));
    $nextWeekEnd = date('Y-m-d', strtotime('sunday next week'));
    $thisMonthStart = date('Y-m-d', strtotime('first day of this month'));
    $thisMonthEnd = date('Y-m-d', strtotime('last day of this month'));
    $lastMonthStart = date('Y-m-d', strtotime('first day of last month'));
    $lastMonthEnd = date('Y-m-d', strtotime('last day of last month'));
    $nextMonthStart = date('Y-m-d', strtotime('first day of next month'));
    $nextMonthEnd = date('Y-m-d', strtotime('last day of next month'));
    $thisYearStart = date('Y-m-d', strtotime('first day of january this year'));
    $thisYearEnd = date('Y-m-d', strtotime('last day of december this year'));
    $lastYearStart = date('Y-m-d', strtotime('first day of january last year'));
    $lastYearEnd = date('Y-m-d', strtotime('last day of december last year'));
    $nextYearStart = date('Y-m-d', strtotime('first day of january next year'));
    $nextYearEnd = date('Y-m-d', strtotime('last day of december next year'));
    $pastAllStart = date('Y-m-d', strtotime('-10 years'));
    $pastAllEnd = date('Y-m-d', strtotime('yesterday'));
    $futureAllStart = date('Y-m-d', strtotime('tomorrow'));
    $futureAllEnd = date('Y-m-d', strtotime('+10 years'));

    switch ($option) {
        case 'today':
            $range = [$today, $today];
            break;
        case 'yesterday':
            $range = [$yesterday, $yesterday];
            break;
        case 'tomorrow':
            $range = [$tomorrow, $tomorrow];
            break;
        case 'this_week':
            $range = [$thisWeekStart, $thisWeekEnd];
            break;
        case 'last_week':
            $range = [$lastWeekStart, $lastWeekEnd];
            break;
        case 'next_week':
            $range = [$nextWeekStart, $nextWeekEnd];
            break;
        case 'this_month':
            $range = [$thisMonthStart, $thisMonthEnd];
            break;
        case 'last_month':
            $range = [$lastMonthStart, $lastMonthEnd];
            break;
        case 'next_month':
            $range = [$nextMonthStart, $nextMonthEnd];
            break;
        case 'this_year':
            $range = [$thisYearStart, $thisYearEnd];
            break;
        case 'last_year':
            $range = [$lastYearStart, $lastYearEnd];
            break;
        case 'next_year':
            $range = [$nextYearStart, $nextYearEnd];
            break;
        case 'past_all':
            $range = [$pastAllStart, $pastAllEnd];
            break;
        case 'future_all':
            $range = [$futureAllStart, $futureAllEnd];
            break;
    }

    return $range;
}


function convertfilesize($bytes)
{
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' Bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' Byte';
    } else {
        $bytes = '0 bytes';
    }

    return $bytes;
}

function timeDelayInHMS($time1, $time2)
{

    $workDuration = "00:00:00";
    $start_time = new \DateTime($time1);
    $end_time = new \DateTime($time2);
    $diff = $end_time->diff($start_time);
    $workDuration = $diff->format('%H:%I:%S');
    return $workDuration;
}


function bloodGroups()
{
    return ["A+", "A-", "B+", "B-", "AB+", "AB-", "O+", "O-"];
}

function getNameTitleOptions()
{
    return ["Mr.", "Miss.", "Mrs.", "Ms.", "Dr.", "Prof."];
}

function filterMobileNumber($number)
{
    // Remove all non-numeric characters
    $number = preg_replace('/\D/', '', $number);

    // Ensure it doesn't exceed 15 digits (E.164 max length)
    return substr($number, -15);
}

function calculateHoursWorked($startTime, $endTime)
{
    // Convert the MySQL timestamps to DateTime objects
    $startDateTime = new DateTime($startTime);
    $endDateTime = new DateTime($endTime);

    // Calculate the interval between the start and end times
    $interval = $endDateTime->diff($startDateTime);

    // Get the total number of minutes in the interval
    $totalMinutes = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;

    // Convert minutes to hours in decimal format
    $totalHours = $totalMinutes / 60;

    return $totalHours;
}


function sanitizeData($data)
{
    // Function to remove non-compatible characters
    $removeInvalidChars = function ($string) {
        // return preg_replace('/[^\x{0000}-\x{D7FF}\x{E000}-\x{FFFF}\x{10000}-\x{10FFFF}]/u', '', $string);
        return preg_replace('/[^\P{C}\P{So}\P{Co}\P{Cs}\P{Cn}\w\s\.\,\-\@\!\?\:\;\(\)\'\"\<\>\/]/u', '', $string);
    };

    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = sanitizeData($value);
        }
    } elseif (is_object($data)) {
        foreach ($data as $key => $value) {
            $data->$key = sanitizeData($value);
        }
    } elseif (is_string($data)) {
        $data = trim($removeInvalidChars($data));
    }

    return $data;
}

function switchCategoryDropdown($itemId, $currentValue, $cateogryList)
{
    // Open the dropdown
    $dropdown = '<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
    foreach ($cateogryList as $k => $v) {

        if ($currentValue == $k) {
            continue;
        }

        // Add a dropdown item for each category
        $dropdown .= '<a title="Switch To ' . $v . '" href="javascript:;" data-endpoint="api/newSample/switchPriority/' . setkey($itemId, "newSample") . '/' . $k . '" data-title="' . $v . '" class="dropdown-item text-primary cursor-pointer apiAction">Switch To: ' . $v . '</a>';
    }
    // Close the dropdown
    $dropdown .= '</ul>';
    return $dropdown;
}


function manageScreenId($id, $serialNo = 0)
{
    $config = config("AppConfig");
    $icon = "<i class='fa fa-bars'></i>";
    if (isset($config->manageScreenIdIcon) and !empty($config->manageScreenIdIcon)) {
        $icon = "<i class='{$config->manageScreenIdIcon}'></i>";
    }
    if ($config->manageScreenIdType == "iconOnly") {
        return $icon;
    } else if ($config->manageScreenIdType == "idWithIcon") {
        return "$serialNo $icon";
    } else if ($config->manageScreenIdType == "iconWithId") {
        return "$icon $serialNo";
    } else {
        return $serialNo;
    }

    // return "<i class='fa fa-bars'></i>";
    return $serialNo;
}


function sampleProfilePicUrl($newSampleId)
{
    if (file_exists(FCPATH . "uploads/sample/photo/" . md5($newSampleId) . ".png")) {
        return base_url("uploads/sample/photo/" . md5($newSampleId) . ".png");
    }
    return base_url("assets/img/user.png");
}
function tenantMasterDropdown($tenantId)
{
    $dropdown = '<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
    $hasActions = false;

    $dropdown .= '<a title="Edit Tenant" target="_blank" href="' . base_url("tenantMaster/editTenant/" . setkey($tenantId, "tenantMaster")) . '" class="dropdown-item text-primary"><i class="fa fa-pencil-alt"></i> Edit</a>';

    $dropdown .= '</ul>';
    $hasActions = true;

    if (!$hasActions) {
        $dropdown .= '<li class="dropdown-item text-muted">No Actions Available</li>';
    }

    return $dropdown;
}

function reminderDropdown($reminderId)
{
    $dropdown = '<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
    $hasActions = false;

    $dropdown .= " <a class='apiPopup dropdown-item text-primary'  href='javascript:;' data-title='Edit Reminder' data-size='lg' data-endpoint='" . ("reminder/addReminder/" . setKey($reminderId, "reminder")) . "'><i class='fa fa-edit'></i> Edit </a>";

    $hasActions = true;
    if (!$hasActions) {
        $dropdown .= '<li class="dropdown-item text-muted">No Actions Available</li>';
    }
    $dropdown .= '</ul>';

    return $dropdown;
}

function getBrowserFingerprint(): string
{
    $agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
    $fingerprint = base64_encode($agent . $lang . ($_SERVER['HTTP_X_DEVICE_FINGERPRINT'] ?? ''));
    return substr(sha1($fingerprint), 0, 32);
}

function resolveAaguid(string $aaguid): ?array
{
    static $map = null;

    if ($map === null) {
        $path = ROOTPATH . 'combined_aaguid.json';
        if (!is_file($path)) {
            return null;
        }

        $json = file_get_contents($path);
        $map = json_decode($json, true) ?? [];
    }

    // Normalize to lowercase with dashes
    $key = strtolower($aaguid);

    $iconLight = 'data:image/svg+xml;base64,' . base64_encode(
        '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10" fill="#e0e0e0"/>
        <text x="12" y="16" font-size="12" text-anchor="middle" fill="#666" font-family="sans-serif">?</text>
    </svg>'
    );


    $iconDark = 'data:image/svg+xml;base64,' . base64_encode(
        '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10" fill="#333"/>
        <text x="12" y="16" font-size="12" text-anchor="middle" fill="#ccc" font-family="sans-serif">?</text>
    </svg>'
    );

    return $map[$key] ?? [
        'name' => 'Unknown Device',
        'icon_dark' => $iconDark,
        'icon_light' => $iconLight,
    ];
}
function getFriendlyDeviceName(string $aaguidHex): array
{
    $aaguidHex = bin2hex(base64_decode($aaguidHex));
    $finalHex = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split($aaguidHex, 4));
    $info = resolveAaguid($finalHex);

    return $info;
}

function assignSerialNumber($tenantId, string $tableName, string $primaryKeyField, int $recordId): ?int
{
    $db = \Config\Database::connect();

    // STEP 1: Check if table exists and has serialNo column
    if (! $db->tableExists($tableName)) return null;

    $fields = $db->getFieldNames($tableName);
    if (! in_array('serialNo', $fields)) return null;

    // STEP 2: Start transaction for safe serial generation
    $builder = $db->table('serialNumbers');
    $db->transStart();

    $row = $builder->where(['tenantId' => $tenantId, 'tableName' => $tableName])->get()->getRow();

    if ($row) {
        $newSerial = $row->serialNumber + 1;
        $builder->where('serialId', $row->serialId)->update([
            'serialNumber' => $newSerial,
            'updatedAt' => date('Y-m-d H:i:s')
        ]);
    } else {
        // Get max serialNo from the table for this tenant
        $maxSerial = $db->table($tableName)
            ->selectMax('serialNo')
            ->where('tenantId', $tenantId)
            ->get()
            ->getRow('serialNo') ?? 0;

        $newSerial = $maxSerial + 1;

        $builder->insert([
            'tenantId' => $tenantId,
            'tableName' => $tableName,
            'serialNumber' => $newSerial,
            'createdAt' => date('Y-m-d H:i:s'),
            'updatedAt' => date('Y-m-d H:i:s')
        ]);
    }

    // STEP 3: Update serialNo in target table
    $db->table($tableName)->where($primaryKeyField, $recordId)->update(['serialNo' => $newSerial]);

    $db->transComplete();
    return $newSerial;
}

function maskString(?string $input, string $maskChar = '*'): string
{
    if (empty($input)) {
        return '';
    }

    $len = mb_strlen($input);
    if ($len <= 2) {
        return str_repeat($maskChar, $len);
    }

    // Calculate number of chars to mask: at least 50%
    $maskCount = max(ceil($len / 2), $len - 4);
    $start = floor(($len - $maskCount) / 2);

    $masked = mb_substr($input, 0, $start)
        . str_repeat($maskChar, $maskCount)
        . mb_substr($input, $start + $maskCount);

    return $masked;
}

function printArrayAsTable($array)
{
    if (empty($array) || !is_array($array)) {
        echo "No data to display.";
        return;
    }

    echo "<table border='1' cellpadding='5' cellspacing='0'>";

    // Table Header
    echo "<thead><tr>";
    foreach (array_keys((array) $array[0]) as $header) {
        echo "<th>" . htmlspecialchars($header) . "</th>";
    }
    echo "</tr></thead>";

    // Table Body
    echo "<tbody>";
    foreach ($array as $row) {
        echo "<tr>";
        foreach ((array) $row as $cell) {
            echo "<td>" . htmlspecialchars((string)$cell) . "</td>";
        }
        echo "</tr>";
    }
    echo "</tbody></table>";
}
