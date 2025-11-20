<?php

use App\Models\Academic\SubjectAssignChildren;
use App\Models\Currency;
use App\Models\Examination\ExaminationSettings;
use App\Models\Examination\MarksGrade;
use App\Models\Language;
use App\Models\Setting;
use App\Models\StudentInfo\SessionClassStudent;
use Modules\MainApp\Entities\Subscription;
use App\Models\SystemNotification;
use App\Models\Upload;
use App\Models\WebsiteSetup\OnlineAdmissionSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\MainApp\Enums\PackagePaymentType;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Twilio\Rest\Client;

function getPagination($ITEM)
{
    return view('common.pagination', compact('ITEM'));
}


/**
 * Get setting value for the current school context
 *
 * This helper respects school context through SchoolScope automatically applied to Setting model.
 * - School users (school_id NOT NULL): Only see their school's settings
 * - System Admin (school_id NULL): Sees all settings (no filtering applied)
 *
 * Cache keys include school_id to prevent cross-school data leakage
 *
 * @param string $name Setting name to retrieve
 * @return mixed|null Setting value or null if not found
 */
function setting($name)
{
    try {
        // Build school-aware cache key to prevent cross-school data leakage
        // System Admin (NULL school_id) gets separate cache namespace
        $schoolId = auth()->check() ? (auth()->user()->school_id ?? 'admin') : 'guest';
        $cacheKey = "setting_{$name}_school_{$schoolId}";

        // Cache for 1 hour with school-specific key
        return Cache::remember($cacheKey, 3600, function () use ($name) {
            // Special handling for currency_symbol
            if ($name == 'currency_symbol') {
                // SchoolScope automatically filters both queries by school_id
                $currencyCode = Setting::where('name', 'currency_code')->first()?->value;
                return Currency::where('code', $currencyCode)->first()?->symbol;
            }

            // SchoolScope automatically filters this query by authenticated user's school_id
            // System Admin (school_id = NULL) will see all settings (no scope filtering)
            // School users (school_id NOT NULL) will only see their school's settings
            $setting_data = Setting::where('name', $name)->first();

            return $setting_data?->value;
        });
    } catch (\Throwable $th) {
        return null;
    }
}

function settingLocale($name)
{
    $setting_data = Setting::where('name', $name)->first();
    if ($setting_data) {
        return @$setting_data->defaultTranslate->value;
    }

    return null;
}

function examSetting($name)
{
    $setting_data = ExaminationSettings::where('name', $name)->where('session_id', setting('session'))->first();
    if ($setting_data) {
        return $setting_data->value;
    }

    return null;
}


function findDirectionOfLang()
{
    $data = Language::where('code', Session::get('locale'))->select('direction')->first();
    return @$data->direction != null ? strtolower(@$data->direction) : '';
}

// for menu active
if (!function_exists('set_menu')) {
    function set_menu(array $path, $active = 'mm-active')
    {
        foreach ($path as $route) {
            if (Route::currentRouteName() == $route) {
                return $active;
            }
        }
        return (request()->is($path)) ? $active : '';
        // return call_user_func_array('Request::is', (array) $path) ? $active : '';
    }
}

// for  submenu list item active
if (!function_exists('menu_active_by_route')) {
    function menu_active_by_route($route)
    {
        return request()->routeIs($route) ? 'mm-show' : 'in-active';
    }
}


// get upload path
if (!function_exists('uploadPath')) {
    function uploadPath($id)
    {
        $row = Upload::find($id);
        return $row->path;
    }
}

if (!function_exists('calculateTax')) {
    /**
     * Calculate tax amount based on school-specific tax settings
     *
     * IMPORTANT: This function respects school context through setting() helper
     * Each school can have different tax_min_amount and tax_percentage values
     *
     * @param float $amount Amount to calculate tax for
     * @return float Calculated tax amount
     */
    function calculateTax($amount)
    {
        try {
            // Use setting() helper which is school-aware through SchoolScope
            // REMOVED static cache to prevent cross-school data leakage
            $taxMinAmount = (float) setting('tax_min_amount');
            $taxPercentage = (float) setting('tax_percentage');

            $tax = 0;

            if ($taxMinAmount && $taxPercentage && $amount >= $taxMinAmount) {
                $tax = ($taxPercentage / 100) * $amount;
            }

            return $tax;
        } catch (\Throwable $th) {
            return 0;
        }
    }
}

if (!function_exists('calculateDiscount')) {
    function calculateDiscount($amount, $percent)
    {
        return ($amount * $percent) / 100;
    }
}

function ___($key = null, $replace = [], $locale = null)
{
    $input = explode('.', $key);
    $term = $input[1] ?? $key;
    $app_local = Session::get('locale') ?: 'bn';

    try {
        if (str_contains($key, '.')) {
            [$file_name, $trans_key] = explode('.', $key, 2); // allow for multiple dots

            $file_path = base_path('lang/' . $app_local . '/' . $file_name . '.json');
        }

        if (!file_exists($file_path)) {
            file_put_contents($file_path, json_encode([]));
        }

        $file_data = json_decode(file_get_contents($file_path), true) ?? [];

        if (!array_key_exists($trans_key, $file_data)) {
            // Transform: replace underscores and convert to title case
            $default_value = ucwords(str_replace('_', ' ', $trans_key));

            $file_data[$trans_key] = $default_value;
            file_put_contents($file_path, json_encode($file_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        return $file_data[$trans_key];
    } catch (\Exception $e) {
        return ucwords(str_replace('_', ' ', $term));
    }
}



// function ___($key = null, $replace = [], $locale = null)
// {

//     $input = explode('.', $key);
//     $file = $input[0];
//     $term = $input[1] ?? '';
//     $app_local = Session::get('locale') ? Session::get('locale') : 'en';
//     $file_path = base_path('lang/' . $app_local . '/' . $file . '.json');
//     $term = str_replace('_', ' ', $term);

//     if (!is_dir(dirname($file_path))) {
//         mkdir(dirname($file_path), 0777, true);
//     }
//     if (!file_exists($file_path)) {
//         $data = [];
//         file_put_contents($file_path, json_encode($data, JSON_UNESCAPED_UNICODE));
//     }

//     $jsonString = file_get_contents($file_path);
//     $data = json_decode($jsonString, true);

//     if (@$data[$term]) {
//         return $data[$term];
//     } else {
//         $data[$term] = $term;
//         file_put_contents($file_path, json_encode($data, JSON_UNESCAPED_UNICODE));
//     }

//     return $term;
// }

// Convert number to words for receipts
if (!function_exists('numberToWords')) {
    function numberToWords($number)
    {
        $hyphen      = '-';
        $conjunction = ' and ';
        $separator   = ', ';
        $negative    = 'negative ';
        $decimal     = ' point ';
        $dictionary  = [
            0                   => 'zero',
            1                   => 'one',
            2                   => 'two',
            3                   => 'three',
            4                   => 'four',
            5                   => 'five',
            6                   => 'six',
            7                   => 'seven',
            8                   => 'eight',
            9                   => 'nine',
            10                  => 'ten',
            11                  => 'eleven',
            12                  => 'twelve',
            13                  => 'thirteen',
            14                  => 'fourteen',
            15                  => 'fifteen',
            16                  => 'sixteen',
            17                  => 'seventeen',
            18                  => 'eighteen',
            19                  => 'nineteen',
            20                  => 'twenty',
            30                  => 'thirty',
            40                  => 'fourty',
            50                  => 'fifty',
            60                  => 'sixty',
            70                  => 'seventy',
            80                  => 'eighty',
            90                  => 'ninety',
            100                 => 'hundred',
            1000                => 'thousand',
            1000000             => 'million',
            1000000000          => 'billion',
            1000000000000       => 'trillion',
            1000000000000000    => 'quadrillion',
            1000000000000000000 => 'quintillion'
        ];

        if (!is_numeric($number)) {
            return false;
        }

        if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
            // overflow
            trigger_error(
                'numberToWords only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
                E_USER_WARNING
            );
            return false;
        }

        if ($number < 0) {
            return $negative . numberToWords(abs($number));
        }

        $string = $fraction = null;

        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number, 2);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens   = ((int) ($number / 10)) * 10;
                $units  = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds  = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . numberToWords($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = numberToWords($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= numberToWords($remainder);
                }
                break;
        }

        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal;
            $words = [];
            foreach (str_split((string) $fraction) as $digit) {
                $words[] = $dictionary[$digit];
            }
            $string .= implode(' ', $words);
        }

        return $string;
    }
}

// global thumbnails
if (!function_exists('globalAsset')) {
    function globalAsset($path, $default_image = null)
    {
        // Handle empty path
        if (empty($path)) {
            return asset("backend/uploads/default-images/" . ($default_image ?? 'user2.jpg'));
        }

        try {
            $fileSystem = setting('file_system') ?? 'local';

            // S3 Storage
            if ($fileSystem == "s3" && Storage::disk('s3')->exists($path)) {
                return Storage::disk('s3')->url($path);
            }

            // Local Storage - Use public_path() to check correct directory & asset() for direct URL
            if ($fileSystem == "local" && file_exists(public_path($path))) {
                return asset($path);
            }

            // Fallback to default image
            return asset("backend/uploads/default-images/" . ($default_image ?? 'user2.jpg'));

        } catch (\Exception $e) {
            \Log::warning("globalAsset failed for path: {$path}", ['error' => $e->getMessage()]);
            return asset("backend/uploads/default-images/" . ($default_image ?? 'user2.jpg'));
        }
    }
}


// Permission check
if (!function_exists('hasPermission')) {
    function hasPermission($keyword)
    {
        // Special handling for exam_entry_publish - only roles 1 and 2
        if ($keyword === 'exam_entry_publish') {
            Log::info('🔍 PUBLISH CHECK: Starting permission check', [
                'keyword' => $keyword,
                'user_id' => Auth::user()->id ?? 'guest',
                'role_id' => Auth::user()->role_id ?? 'none',
                'is_authenticated' => Auth::check()
            ]);

            if (Auth::check() && in_array(Auth::user()->role_id, [1, 2])) {
                Log::info('✅ PUBLISH CHECK: Role validation passed', [
                    'role_id' => Auth::user()->role_id
                ]);

                // Both Super Admin and Admin need permission in array
                $hasPermission = in_array($keyword, Auth::user()->permissions ?? []);
                Log::info('🔍 PUBLISH CHECK: Permission array check', [
                    'role_id' => Auth::user()->role_id,
                    'has_permission' => $hasPermission,
                    'permissions' => Auth::user()->permissions
                ]);
                return $hasPermission;
            }

            Log::warning('❌ PUBLISH CHECK: Role validation failed - DENIED', [
                'role_id' => Auth::user()->role_id ?? 'none',
                'required_roles' => [1, 2]
            ]);
            return false;
        }

        // Special handling for exam_entry_delete - only roles 1 and 2
        if ($keyword === 'exam_entry_delete') {
            if (Auth::check() && in_array(Auth::user()->role_id, [1, 2])) {
                // Both Super Admin and Admin need permission in array
                return in_array($keyword, Auth::user()->permissions ?? []);
            }
            return false;
        }

        // Special handling for cash_transfer_approve - only roles 1 and 2
        if ($keyword === 'cash_transfer_approve') {
            if (Auth::check() && in_array(Auth::user()->role_id, [1, 2])) {
                // Both Super Admin and Regular Admin need permission in array
                return in_array($keyword, Auth::user()->permissions ?? []);
            }
            return false;
        }

        // Special handling for cash_transfer_delete - only roles 1 and 2
        if ($keyword === 'cash_transfer_delete') {
            if (Auth::check() && in_array(Auth::user()->role_id, [1, 2])) {
                // Both Super Admin and Regular Admin need permission in array
                return in_array($keyword, Auth::user()->permissions ?? []);
            }
            return false;
        }

        // Super Admin bypass - role_id 1 has all permissions
        if (Auth::check() && Auth::user()->role_id == 1) {
            return true;
        }

        // Default permission check
        if (in_array($keyword, Auth::user()->permissions ?? [])) {
            return true;
        }
        return false;
    }
}


// Date format
if (!function_exists('dateFormat')) {
    function dateFormat($keyword)
    {
        return date('d M Y', strtotime($keyword));
    }
}
if (!function_exists('timeFormat')) {
    function timeFormat($keyword)
    {
        return date('g:i A', strtotime($keyword));
    }
}
// Mark grade
if (!function_exists('markGrade')) {
    function markGrade($data)
    {
        $result = MarksGrade::where('session_id', setting('session'))->where('percent_upto', '>=', $data)->where('percent_from', '<=', $data)->first();
        if ($result) {
            return $result->name;
        }
        return '...';
    }
}

if (!function_exists('userTheme')) {
    function userTheme()
    {
        $session_theme = Session::get('user_theme');

        if (isset($session_theme)) {
            return $session_theme;
        } else {
            return 'default-theme';
        }
    }
}

if (!function_exists('leadingZero')) {
    function withLeadingZero($number)
    {

        // $strNumber = $number;
        // if(strlen($strNumber) < 10){
        //     return $strNumber;
        // }

        return $number;
    }
}


if (!function_exists('setEnvironmentValue')) {
    function setEnvironmentValue($envKey, $envValue)
    {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);

        $str .= "\n"; // In case the searched variable is in the last line without \n
        $keyPosition = strpos($str, "{$envKey}=");
        $endOfLinePosition = strpos($str, PHP_EOL, $keyPosition);
        $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);
        $str = str_replace($oldLine, "{$envKey}={$envValue}", $str);
        $str = substr($str, 0, -1);

        $fp = fopen($envFile, 'w');
        fwrite($fp, $str);
        fclose($fp);
    }
}

if (!function_exists('s3Upload')) {
    function s3Upload($directory, $file)
    {
        $directory = 'public/' . $directory;
        return Storage::disk('s3')->put($directory, $file, 'public');
    }
}

if (!function_exists('s3ObjectCheck')) {
    function s3ObjectCheck($path)
    {
        return Storage::disk('s3')->exists($path);
    }
}


if (!function_exists('include_route_files')) {
    /**
     * Loops through a folder and requires all PHP files
     * Searches sub-directories as well.
     *
     * @param $folder
     */
    function include_route_files($folder)
    {
        try {
            $rdi = new RecursiveDirectoryIterator($folder);
            $it = new RecursiveIteratorIterator($rdi);

            while ($it->valid()) {
                if (!$it->isDot() && $it->isFile() && $it->isReadable() && $it->current()->getExtension() === 'php') {
                    require $it->key();
                }

                $it->next();
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    function getAllDaysInMonth($year, $month)
    {
        $daysInMonth = Carbon::create($year, $month)->daysInMonth;
        $days = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create($year, $month, $day);
            $days[] = $date->format('Y-m-d');
        }

        return $days;
    }


    function getSubdomainName()
    {
        $parsedUrl = parse_url(url()->full());
        $hostParts = explode('.', $parsedUrl['host']);
        return $hostParts;
    }
}

if (!function_exists('saasMiddleware')) {
    function saasMiddleware()
    {


        if (env('APP_SAAS')) {
            return [
                'web',
                InitializeTenancyByDomain::class,
                PreventAccessFromCentralDomains::class,
            ];
        }

        return [
            'web'
        ];
    }
}

if (!function_exists('saasApiMiddleware')) {
    function saasApiMiddleware()
    {

        if (env('APP_SAAS')) {
            return [
                'api',
                InitializeTenancyByDomain::class,
                PreventAccessFromCentralDomains::class,
            ];
        }

        return [
            'api'
        ];
    }
}


function activeSubscriptionStudentLimit()
{
    if (env('APP_SAAS')) {
        return cache()->rememberForever('activeSubscriptionStudentLimit', function () {
            $subscription = Subscription::active()->first();

            if ($subscription) {
                return $subscription->payment_type == PackagePaymentType::PREPAID ? $subscription->student_limit : 99999999;
            }

            return null;
        });
    }

    return null;
}

function activeSubscriptionStaffLimit()
{
    if (env('APP_SAAS')) {
        return cache()->rememberForever('activeSubscriptionStaffLimit', function () {
            $subscription = Subscription::active()->first();

            if ($subscription) {
                return $subscription->payment_type == PackagePaymentType::PREPAID ? $subscription->staff_limit : 99999999;
            }

            return null;
        });
    }

    return null;
}

function activeSubscriptionExpiryDate()
{
    if (env('APP_SAAS')) {
        return cache()->rememberForever('activeSubscriptionExpiryDate', function () {
            $subscription = Subscription::active()->first();
            if ($subscription) {
                if ($subscription->expiry_date) { // expiry gate null menas this is lifetime package
                    if (date('Y-m-d') <= date('Y-m-d', strtotime($subscription->expiry_date))) {
                        return true;
                    }
                    return false;
                }
                return true;
            }
            return false;
        });
    }
    return true;
}

/**
 * @deprecated This function is deprecated and should not be used.
 * It was using a broken global cache that returned ANY school's subscription features.
 * Use Auth::user()->school->hasFeatureAccess($feature) or hasFeature() helper instead.
 *
 * Kept for backward compatibility only - returns empty array.
 */
function activeSubscriptionFeatures()
{
    // Return empty array to prevent breaking existing code
    // All feature checks should now use the school-aware hasFeature() function
    return [];
}


// Feature check - School-aware feature access with package-based restrictions
if (!function_exists('hasFeature')) {
    /**
     * Check if current user's school has access to a feature
     *
     * This function uses package-based detection to determine if feature
     * restrictions should apply. Schools WITHOUT a package get all features
     * (single-school mode), while schools WITH a package have restrictions
     * enforced based on their package's permission features.
     *
     * @param string $keyword Feature attribute identifier
     * @return bool True if user has access to feature
     */
    function hasFeature(string $keyword): bool
    {
        // LOG POINT 1: Function entry
        Log::channel('feature_access')->debug('hasFeature() called', [
            'feature_keyword' => $keyword,
            'timestamp' => now()->toDateTimeString(),
            'request_url' => request()->url(),
            'request_method' => request()->method(),
        ]);

        // User must be authenticated
        if (!Auth::check()) {
            // LOG POINT 2: Unauthenticated access
            Log::channel('feature_access')->warning('hasFeature() - No authenticated user', [
                'feature_keyword' => $keyword,
                'result' => false,
            ]);
            return false;
        }

        $user = Auth::user();

        // LOG POINT 3: User context
        Log::channel('feature_access')->debug('hasFeature() - User context', [
            'feature_keyword' => $keyword,
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_role_id' => $user->role_id,
            'user_school_id' => $user->school_id,
        ]);

        // System admins (school_id === null) have full access
        if ($user->school_id === null) {
            // LOG POINT 4: System admin access
            Log::channel('feature_access')->info('hasFeature() - System admin detected', [
                'feature_keyword' => $keyword,
                'user_id' => $user->id,
                'user_role_id' => $user->role_id,
                'user_school_id' => $user->school_id,
                'result' => true,
                'reason' => 'system_admin_full_access',
            ]);
            return true;
        }

        // LOG POINT 5: School relationship check
        $hasSchoolRelation = $user->school !== null;
        Log::channel('feature_access')->debug('hasFeature() - School relationship', [
            'feature_keyword' => $keyword,
            'user_id' => $user->id,
            'has_school_relation' => $hasSchoolRelation,
            'school_id_from_user' => $user->school_id,
        ]);

        // School users MUST have a valid school and package in SaaS mode
        // No package = no feature access (enforce subscription-based restrictions)
        if (!$user->school) {
            // LOG POINT 6a: No school relationship - deny access
            Log::channel('feature_access')->info('hasFeature() - No school relationship', [
                'feature_keyword' => $keyword,
                'user_id' => $user->id,
                'user_school_id' => $user->school_id,
                'result' => false,
                'reason' => 'no_school_relationship',
            ]);
            return false;
        }

        if ($user->school->package_id === null) {
            // LOG POINT 6b: No package assigned - deny access (SaaS mode)
            Log::channel('feature_access')->info('hasFeature() - No package assigned', [
                'feature_keyword' => $keyword,
                'user_id' => $user->id,
                'user_school_id' => $user->school_id,
                'school_id' => $user->school->id,
                'school_package_id' => null,
                'result' => false,
                'reason' => 'no_package_saas_mode',
            ]);
            return false;
        }

        // LOG POINT 7: Package-based restriction enforcement
        Log::channel('feature_access')->info('hasFeature() - Package restriction check', [
            'feature_keyword' => $keyword,
            'user_id' => $user->id,
            'user_role_id' => $user->role_id,
            'school_id' => $user->school->id,
            'school_package_id' => $user->school->package_id,
            'package_name' => $user->school->package?->name,
        ]);

        // School users WITH a package: enforce package-based feature restrictions
        $hasAccess = $user->school->hasFeatureAccess($keyword);

        // LOG POINT 8: Final result
        Log::channel('feature_access')->info('hasFeature() - Final result', [
            'feature_keyword' => $keyword,
            'user_id' => $user->id,
            'school_id' => $user->school->id,
            'school_package_id' => $user->school->package_id,
            'has_access' => $hasAccess,
            'reason' => $hasAccess ? 'package_allows_feature' : 'package_denies_feature',
        ]);

        return $hasAccess;
    }
}

/**
 * Check if user has access to ANY of the provided features.
 * Useful for checking feature group access (e.g., if user has any fees-related feature).
 *
 * @param array $featureKeywords Array of feature keywords to check
 * @return bool True if user has access to at least one feature
 */
if (!function_exists('hasAnyFeature')) {
    function hasAnyFeature(array $featureKeywords): bool
    {
        if (empty($featureKeywords)) {
            return false;
        }

        foreach ($featureKeywords as $keyword) {
            if (hasFeature($keyword)) {
                return true;
            }
        }

        return false;
    }
}


function sessionClassStudent()
{

    $sesionClassStudent = SessionClassStudent::query()
        ->where('student_id', request()->filled('student_id') ? request('student_id') : @auth()->user()->student->id)
        ->first();

    if ($sesionClassStudent) {
        return $sesionClassStudent;
    }

    if (sessionClassStudentByParent()) {
        return sessionClassStudentByParent();
    }

    if (isStudentAccessInAPI()) {
        $data = SessionClassStudent::query()
            ->where('student_id', request()->filled('student_id') ? request('student_id') : @auth()->user()->student->id)
            ->whereHas('session', function ($q) {
                $q->whereYear('start_date', '<=', date('Y'))
                    ->whereYear('end_date', '>=', date('Y'));
            })->first();

        return $data;
    }

    return null;
}


function getDayNum($date)
{
    $day = Str::lower(Carbon::createFromFormat('Y-m-d', $date)->format('l'));

    switch ($day) {
        case $day == 'saturday':
            return 1;
            break;
        case $day == 'sunday':
            return 2;
            break;
        case $day == 'monday':
            return 3;
            break;
        case $day == 'tuesday':
            return 4;
            break;
        case $day == 'wednesday':
            return 5;
            break;
        case $day == 'thursday':
            return 6;
            break;
        default:
            return 7;
    }
}


function loadPayPalCredentials()
{
    if (Str::lower(Setting('paypal_payment_mode')) == 'sandbox') {
        \Config::set('paypal.sandbox.username', Setting('paypal_sandbox_api_username'));
        \Config::set('paypal.sandbox.password', Setting('paypal_sandbox_api_password'));
        \Config::set('paypal.sandbox.secret', Setting('paypal_sandbox_api_secret'));
        \Config::set('paypal.sandbox.certificate', Setting('paypal_sandbox_api_certificate'));
    } elseif (Str::lower(Setting('paypal_payment_mode')) == 'live') {
        \Config::set('paypal.live.username', Setting('paypal_live_api_username'));
        \Config::set('paypal.live.password', Setting('paypal_live_api_password'));
        \Config::set('paypal.live.secret', Setting('paypal_live_api_secret'));
        \Config::set('paypal.live.certificate', Setting('paypal_live_api_certificate'));
    }
}


function teacherSubjects()
{
    return SubjectAssignChildren::with('subject')
        ->when(Auth::user()->role_id == 5, function ($query) {
            return $query->where('staff_id', Auth::user()->staff->id);
        })
        ->pluck('subject_id')
        ->toArray();
}


if (!function_exists('getAttendanceType')) {

    function getAttendanceType($type)
    {
        if ($type == 1) {
            return 'PRESENT';
        } elseif ($type == 2) {
            return 'LATE';
        } elseif ($type == 3) {
            return 'ABSENT';
        } elseif ($type == 4) {
            return 'HALFDAY';
        } else {
            return '';
        }
    }
}


if (!function_exists('send_web_notification')) {
    function send_web_notification($title, $message, $reciever_id, $url = null)
    {
        try {
            $notification = new SystemNotification();
            $notification->title = $title;
            $notification->message = $message;
            $notification->reciver_id = $reciever_id;
            $notification->url = $url;
            $notification->save();
        } catch (\Throwable $th) {
            Log::info('NOtification store::' . $th);
        }
    }
}

if (!function_exists('send_message')) {
    function send_message_twillo($message, $recipients)
    {
        Log::info('To Number ::' . $recipients . 'Message::  ' . $message);
        try {
            $sid = setting('twilio_account_sid');
            $token = setting('twilio_auth_token');
            $twilio_number = setting('twilio_phone_number');

            if ($sid && $token && $twilio_number) {
                $twilio = new Client($sid, $token);
                return $twilio->messages
                    ->create(
                        $recipients,
                        [
                            "body" => $message,
                            "from" => $twilio_number
                        ]
                    );
            }
        } catch (\Throwable $th) {
            Log::info('Twillo Msg Error' . $th->getMessage());
        }
    }
}


if (!function_exists('send_flutter_notification')) {
    function send_flutter_notification($title, $message, $img = null)
    {

        try {
            $url = 'https://fcm.googleapis.com/fcm/send';
            $dataArr = array('click_action' => 'FLUTTER_NOTIFICATION_CLICK', 'status' => "done");
            $notification = array('title' => $title, 'text' => $message, 'image' => $img, 'sound' => 'default', 'badge' => '1',);
            $arrayToSend = array('notification' => $notification, 'data' => $dataArr, 'priority' => 'high');
            $fields = json_encode($arrayToSend);
            $headers = array(
                'Authorization: key=' . setting('FCM_SECRET_KEY'),
                'Content-Type: application/json'
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            $result = curl_exec($ch);
            curl_close($ch);
        } catch (\Throwable $th) {
            Log::info('Flutter Push Msg Error' . $th->getMessage());
        }
    }
}

function getAttendanceType($type)
{
    if ($type == 1) {
        return 'PRESENT';
    } elseif ($type == 2) {
        return 'LATE';
    } elseif ($type == 3) {
        return 'ABSENT';
    } elseif ($type == 4) {
        return 'HALFDAY';
    } else {
        return '';
    }
}


function send_web_notification($title, $message, $reciever_id, $url = null)
{
    try {
        $notification = new SystemNotification();
        $notification->title = $title;
        $notification->message = $message;
        $notification->reciver_id = $reciever_id;
        $notification->url = $url;
        $notification->save();
    } catch (\Throwable $th) {
        Log::info('NOtification store::' . $th);
        //   Log::info($th->getMessage());
    }
}

if (!function_exists('hasModule')) {
    function hasModule($name): bool
    {
        $filePath = base_path('modules_statuses.json');
        $statuses = json_decode(file_get_contents($filePath), true);
        if (isset($statuses[$name])) {
            $isModuleEnabled = $statuses[$name];
            if ($isModuleEnabled) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}

if (!function_exists('isSuperAdmin')) {
    /**
     * Check if current user is system admin (super admin)
     *
     * System admins have no school_id (null), granting full platform access.
     * School admins have role_id = 1 but school_id != null, and must follow feature restrictions.
     *
     * @return bool True if system admin with no school context
     */
    function isSuperAdmin(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        // Only users without school_id are true system admins
        return auth()->user()->school_id === null;
    }
}

if (!function_exists('isSchoolAdmin')) {
    /**
     * Check if current user is a school administrator
     *
     * School Admin Definition:
     * - Has role_id = 1 (Super Admin/Admin role)
     * - Has school_id NOT NULL (belongs to a specific school)
     * - Can manage their school but not create/delete branches
     *
     * @return bool True if school admin with school context
     */
    function isSchoolAdmin(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        $user = auth()->user();

        // School admin has role_id = 1 AND has a school_id (school context)
        return $user->role_id === 1 && $user->school_id !== null;
    }
}


if (!function_exists('admission_fields')) {
    function admission_fields()
    {
        // dd(OnlineAdmissionSetting::where('type', 'online_admission')->where('is_show',1)->get()->pluck('field')->values(),2);
        try {
            if (Cache::has('online_admission_field_is_show') && Cache::get('online_admission_field_is_show')) {
                return Cache::get('online_admission_field_is_show');
            }
            return Cache::rememberForever('online_admission_setting', function () {
                return OnlineAdmissionSetting::where('type', 'online_admission')->where('is_show', 1)->get()->pluck(['field'])->toArray();
            });
        } catch (\Throwable $th) {
            return [];
        }
    }
}

if (!function_exists('is_show')) {
    function is_show($field)
    {
        try {
            $field_array = admission_fields();
            return in_array($field, $field_array);
        } catch (\Throwable $th) {
            dd($th);
            return false;
        }
    }
}

if (!function_exists('is_required')) {
    function is_required($field)
    {
        try {
            $field_array = admission_required_fields();
            return in_array($field, $field_array);
        } catch (\Throwable $th) {
            return false;
        }
    }
}

if (!function_exists('admission_required_fields')) {
    function admission_required_fields()
    {
        try {
            if (Cache::has('online_admission_field_is_require') && Cache::get('online_admission_field_is_require')) {
                return Cache::get('online_admission_field_is_require');
            }
            return Cache::rememberForever('online_admission_field_is_require', function () {
                return OnlineAdmissionSetting::where('type', 'online_admission')->where('is_required', 1)->get()->pluck(['field'])->toArray();
            });
        } catch (\Throwable $th) {
            return [];
        }
    }
}


if (!function_exists('app_translate')) {
    function app_translate()
    {
        try {
            return env('APP_TRANSLATE');
        } catch (\Throwable $th) {
            return false;
        }
    }
}


if (!function_exists('isParentUserAccessStudentInAPI')) {
    function isParentUserAccessStudentInAPI()
    {
        return auth()->check() &&
            @auth()->user()->role_id == 7 &&
            request()->filled('student_id')
            ? true : false;
    }
}


if (!function_exists('isStudentAccessInAPI')) {
    function isStudentAccessInAPI()
    {
        return !isParentUserAccessStudentInAPI() &&
            (
                (auth()->check() && @auth()->user()->role_id == 6) ||
                request()->filled('student_id')
            )
            ? true : false;
    }
}


if (!function_exists('sessionClassStudentByParent')) {
    function sessionClassStudentByParent()
    {

        return SessionClassStudent::query()
            ->where('student_id', request('student_id'))
            ->whereHas('student', fn($q) => $q->where('parent_guardian_id', @auth()->user()->parent->id))
            ->whereHas('session', function ($q) {
                $q->whereYear('start_date', '<=', date('Y'))
                    ->whereYear('end_date', '>=', date('Y'));
            })
            ->first();
    }
}

// encrypt id
if (!function_exists('encryptFunction')) {
    function encryptFunction($number = null)
    {

        return openssl_encrypt($number, "AES-128-CTR", "CodeSpeedyKeybj54HH", 0, '8565825542115032');
    }
};

// decrypt id
if (!function_exists('decryptFunction')) {
    function decryptFunction($encrypted = null)
    {
        return openssl_decrypt($encrypted, "AES-128-CTR", "CodeSpeedyKeybj54HH", 0, '8565825542115032');
    }
};


if (!function_exists('humanReadableDate')) {
    function humanReadableDate($date)
    {
        $date = Carbon::parse($date);

        // Check if the date is within one day
        if ($date->diffInDays() >= 1) {
            // Show in a standard date format (e.g., 'Y-m-d H:i:s')
            $formattedDate = $date->format('jS F Y h:i A');
        } else {
            // Show human-readable time difference (e.g., '2 hours ago')
            $formattedDate = $date->diffForHumans();
        }

        return $formattedDate;
    }
};


if (!function_exists('saasTenantMigrationPaths')) {
    function saasTenantMigrationPaths()
    {
        $filePath = base_path('modules_statuses.json');
        $migrationPaths = [
            database_path('migrations/tenant') // Core tenant migrations
        ];

        if (file_exists($filePath)) {
            $json_content = file_get_contents($filePath);
            $modules = json_decode($json_content, true);

            // Exclude MainApp and Installer
            unset($modules["MainApp"]);
            unset($modules["Installer"]);

            // Loop through only enabled (true) modules
            foreach ($modules as $module => $status) {
                if ($status === true) {
                    $migrationPaths[] = base_path("Modules/$module/Database/Migrations");
                }
            }
        }

        return $migrationPaths;
    }
}

/**
 * Generate avatar HTML for students based on their names
 * Creates circular avatars with initials and consistent color backgrounds
 *
 * @param string $firstName Student's first name
 * @param string $lastName Student's last name
 * @param string $size Size CSS value (e.g., '40px', '60px')
 * @return string HTML markup for the avatar
 */
function generateStudentAvatar($firstName, $lastName, $size = '40px')
{
    // Color palette - professional colors that work well with the system theme
    $colors = [
        ['bg' => '#4A90E2', 'text' => '#FFFFFF'], // Blue
        ['bg' => '#7ED321', 'text' => '#FFFFFF'], // Green
        ['bg' => '#F5A623', 'text' => '#FFFFFF'], // Orange
        ['bg' => '#BD10E0', 'text' => '#FFFFFF'], // Purple
        ['bg' => '#B8E986', 'text' => '#333333'], // Light Green
        ['bg' => '#9013FE', 'text' => '#FFFFFF'], // Violet
        ['bg' => '#50E3C2', 'text' => '#333333'], // Teal
        ['bg' => '#D0021B', 'text' => '#FFFFFF'], // Red
        ['bg' => '#F8E71C', 'text' => '#333333'], // Yellow
        ['bg' => '#8B572A', 'text' => '#FFFFFF'], // Brown
        ['bg' => '#417505', 'text' => '#FFFFFF'], // Dark Green
        ['bg' => '#9B9B9B', 'text' => '#FFFFFF'], // Gray
    ];

    // Clean and prepare names
    $firstName = trim($firstName ?? '');
    $lastName = trim($lastName ?? '');

    // Generate initials
    $initials = '';
    if (!empty($firstName)) {
        $initials .= strtoupper(substr($firstName, 0, 1));
    }
    if (!empty($lastName)) {
        $initials .= strtoupper(substr($lastName, 0, 1));
    }

    // Fallback if no proper names
    if (empty($initials)) {
        $initials = 'ST'; // Student
    }

    // Generate consistent color based on full name hash
    $fullName = $firstName . $lastName;
    $colorIndex = !empty($fullName) ? crc32($fullName) % count($colors) : 0;
    $colorIndex = abs($colorIndex); // Ensure positive index
    $selectedColor = $colors[$colorIndex];

    // Calculate font size based on avatar size
    $numericSize = (int) preg_replace('/[^0-9]/', '', $size);
    $fontSize = max(($numericSize * 0.4), 12) . 'px';

    // Generate avatar HTML
    return sprintf(
        '<div class="student-avatar d-inline-flex align-items-center justify-content-center rounded-circle fw-bold"
              style="width: %s; height: %s; background-color: %s; color: %s; font-size: %s; min-width: %s; min-height: %s; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, sans-serif;">
            %s
        </div>',
        $size,
        $size,
        $selectedColor['bg'],
        $selectedColor['text'],
        $fontSize,
        $size,
        $size,
        htmlspecialchars($initials)
    );
}

/**
 * Get the current active academic year ID
 *
 * CRITICAL: Always returns tenant-specific session, never hardcoded fallback
 * For multi-tenant systems, each school has its own session settings
 */
if (!function_exists('activeAcademicYear')) {
    function activeAcademicYear()
    {
        try {
            return \Cache::remember('active_academic_year', 60, function () {
                // Get school-specific session from settings
                $sessionId = setting('session');

                if (!$sessionId) {
                    // Log warning for missing session configuration
                    \Log::warning('Active academic year not set in settings - using database fallback', [
                        'user_id' => auth()->id() ?? null,
                        'authenticated' => auth()->check()
                    ]);

                    // Fallback: Get the currently active session from database
                    $activeSession = \App\Models\Session::where('status', 1)
                        ->orderBy('id', 'desc')
                        ->value('id');

                    if ($activeSession) {
                        \Log::info('Using active session from database', [
                            'session_id' => $activeSession
                        ]);
                        return $activeSession;
                    }

                    // Last resort: Get any session (with warning)
                    $anySession = \App\Models\Session::orderBy('id', 'desc')->value('id');

                    if ($anySession) {
                        \Log::warning('No active session found - using latest session', [
                            'session_id' => $anySession
                        ]);
                        return $anySession;
                    }

                    // Critical: No sessions exist at all
                    \Log::error('No academic sessions exist in database');
                    throw new \Exception('No academic sessions configured. Please contact administrator.');
                }

                return $sessionId;
            });
        } catch (\Exception $e) {
            \Log::error('Failed to get active academic year', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Try direct database query as final fallback
            try {
                $fallbackSession = \App\Models\Session::where('status', 1)
                    ->orderBy('id', 'desc')
                    ->value('id');

                if ($fallbackSession) {
                    \Log::warning('Using database fallback after cache error', [
                        'session_id' => $fallbackSession
                    ]);
                    return $fallbackSession;
                }

                // Absolute last resort: get any session
                $anySession = \App\Models\Session::orderBy('id', 'desc')->value('id');
                if ($anySession) {
                    return $anySession;
                }
            } catch (\Exception $fallbackError) {
                \Log::critical('Complete failure to retrieve academic year', [
                    'original_error' => $e->getMessage(),
                    'fallback_error' => $fallbackError->getMessage()
                ]);
            }

            // Only if absolutely everything fails
            throw new \Exception('Unable to determine academic year. Please contact administrator.');
        }
    }
}

/**
 * Get the current active branch ID
 */
if (!function_exists('activeBranch')) {
    function activeBranch()
    {
        try {
            return \Cache::remember('active_branch', 60, function () {
                if (\Auth::check() && \Auth::user()->branch_id) {
                    return \Auth::user()->branch_id;
                }
                return 1; // Default branch ID
            });
        } catch (\Exception $e) {
            return 1; // Fallback to ID 1
        }
    }
}

/**
 * Get student limit for a specific branch
 *
 * Returns the per-branch student limit from the active subscription package.
 * In SaaS mode with prepaid packages, this limit applies to EACH branch individually.
 *
 * @param int|null $branchId Optional branch ID. If null, uses current user's branch.
 * @return int Student limit for the branch (0 if no active subscription or unlimited)
 */
if (!function_exists('getBranchStudentLimit')) {
    function getBranchStudentLimit($branchId = null): int
    {
        try {
            // Get branch ID from parameter or current user
            if ($branchId === null) {
                $branchId = activeBranch();
            }

            // Check if branch belongs to a school (not main dashboard)
            $branch = \Modules\MultiBranch\Entities\Branch::find($branchId);
            if (!$branch || !$branch->school_id) {
                return 0; // Unlimited for main dashboard branches
            }

            // Cache key includes branch_id for branch-specific caching
            $cacheKey = "branch_student_limit_{$branchId}";

            return \Cache::remember($cacheKey, 300, function () use ($branch, $branchId) {
                // Query MainApp subscription (SaaS/Central DB) which stores package limits
                // MainApp\Subscription has no global scopes, queries package subscription for school
                $subscription = Subscription::where('school_id', $branch->school_id)
                    ->orderBy('id', 'desc')
                    ->first();

                if (!$subscription) {
                    \Log::warning('No subscription found for school', [
                        'school_id' => $branch->school_id,
                        'branch_id' => $branchId
                    ]);
                    return 0; // No subscription = no limit enforcement
                }

                // Log subscription details for debugging
                \Log::info('Branch student limit check', [
                    'branch_id' => $branchId,
                    'school_id' => $branch->school_id,
                    'subscription_id' => $subscription->id,
                    'package_id' => $subscription->package_id,
                    'student_limit' => $subscription->student_limit,
                    'payment_type' => $subscription->payment_type,
                    'status' => $subscription->status,
                    'payment_status' => $subscription->payment_status
                ]);

                // Check if it's a prepaid package (has limits)
                if ($subscription->payment_type == \Modules\MainApp\Enums\PackagePaymentType::PREPAID) {
                    return $subscription->student_limit ?? 0;
                }

                // Postpaid/unlimited packages
                return 99999999;
            });
        } catch (\Exception $e) {
            \Log::error('Error getting branch student limit', [
                'branch_id' => $branchId ?? null,
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }
}

/**
 * Get current student count for a specific branch
 *
 * Counts active students enrolled in the specified branch.
 * Uses efficient query with proper tenant isolation.
 *
 * @param int|null $branchId Optional branch ID. If null, uses current user's branch.
 * @return int Current number of active students in the branch
 */
if (!function_exists('getBranchCurrentStudentCount')) {
    function getBranchCurrentStudentCount($branchId = null): int
    {
        try {
            // Get branch ID from parameter or current user
            if ($branchId === null) {
                $branchId = activeBranch();
            }

            // Cache key includes branch_id for branch-specific caching
            $cacheKey = "branch_student_count_{$branchId}";

            return \Cache::remember($cacheKey, 300, function () use ($branchId) {
                // Count active students through User model for proper branch filtering
                // Students have role_id = 6 and must have the specified branch_id
                $count = \App\Models\User::where('role_id', 6)
                    ->where('branch_id', $branchId)
                    ->whereHas('student', function ($query) {
                        $query->where('status', \App\Enums\Status::ACTIVE);
                    })
                    ->count();

                return $count;
            });
        } catch (\Exception $e) {
            \Log::error('Error counting branch students', [
                'branch_id' => $branchId ?? null,
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }
}

/**
 * Get remaining student slots for a specific branch
 *
 * Calculates how many more students can be enrolled in the branch
 * based on the subscription limit and current enrollment.
 *
 * @param int|null $branchId Optional branch ID. If null, uses current user's branch.
 * @return int Number of remaining slots (0 if at/over limit or unlimited)
 */
if (!function_exists('getBranchStudentSlotsRemaining')) {
    function getBranchStudentSlotsRemaining($branchId = null): int
    {
        try {
            // Get branch ID from parameter or current user
            if ($branchId === null) {
                $branchId = activeBranch();
            }

            // Check if branch belongs to a school (not main dashboard)
            $branch = \Modules\MultiBranch\Entities\Branch::find($branchId);
            if (!$branch || !$branch->school_id) {
                return 99999999; // Unlimited for main dashboard branches
            }

            $limit = getBranchStudentLimit($branchId);
            $currentCount = getBranchCurrentStudentCount($branchId);

            // If limit is very high (postpaid/unlimited), return high number
            if ($limit >= 99999999) {
                return 99999999;
            }

            // Calculate remaining slots
            $remaining = max(0, $limit - $currentCount);

            return $remaining;
        } catch (\Exception $e) {
            \Log::error('Error calculating branch student slots remaining', [
                'branch_id' => $branchId ?? null,
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }
}

/**
 * Get branch name by ID
 *
 * @param int $branchId Branch ID
 * @return string Branch name or 'Main Branch' if not found
 */
if (!function_exists('getBranchName')) {
    function getBranchName($branchId = null): string
    {
        try {
            if ($branchId === null) {
                $branchId = activeBranch();
            }

            // For now, return generic branch name
            // TODO: Implement actual Branch model lookup when available
            return $branchId == 1 ? 'Main Branch' : "Branch {$branchId}";
        } catch (\Exception $e) {
            return 'Unknown Branch';
        }
    }
}

/**
 * Get active subscription package name
 *
 * @return string Package name or 'No Package' if not found
 */
if (!function_exists('getActivePackageName')) {
    function getActivePackageName(): string
    {
        try {
            $subscription = Subscription::active()->first();

            if (!$subscription) {
                return 'No Active Package';
            }

            // Try to get package name from subscription
            // Adjust this based on your actual subscription-package relationship
            return $subscription->package_name ?? 'Active Package';
        } catch (\Exception $e) {
            return 'Unknown Package';
        }
    }
}

/**
 * Get the appropriate dashboard route based on user role and context
 *
 * This helper provides a single source of truth for dashboard routing across the application.
 * It respects the multi-tenant architecture and directs users to their appropriate dashboard:
 * - System Admin (role_id=0, school_id=NULL) → MainApp dashboard
 * - School users (Super Admin, Admin, Staff, Teachers) → School dashboard
 * - Students → Student panel dashboard
 * - Parents/Guardians → Parent panel dashboard
 *
 * @return string Dashboard route URL
 */
if (!function_exists('getDashboardRoute')) {
    function getDashboardRoute(): string
    {
        try {
            $user = Auth::user();

            // Guest users go to login
            if (!$user) {
                return route('login');
            }

            // System Admin with no school assignment → MainApp dashboard
            if ($user->role_id === \App\Enums\RoleEnum::MAIN_SYSTEM_ADMIN
                && $user->school_id === null) {
                return route('mainapp.dashboard');
            }

            // Student → Student panel dashboard
            if ($user->role_id === \App\Enums\RoleEnum::STUDENT) {
                return route('student-panel-dashboard.index');
            }

            // Parent/Guardian → Parent panel dashboard
            if ($user->role_id === \App\Enums\RoleEnum::GUARDIAN) {
                return route('parent-panel-dashboard.index');
            }

            // School admin, staff, teachers → School dashboard
            return route('dashboard');

        } catch (\Throwable $th) {
            // Fallback to login on any error
            Log::warning('getDashboardRoute error', [
                'error' => $th->getMessage(),
                'user_id' => Auth::id() ?? 'guest'
            ]);
            return route('login');
        }
    }
}
