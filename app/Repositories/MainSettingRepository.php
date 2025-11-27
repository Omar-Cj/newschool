<?php

namespace App\Repositories;

use App\Models\MainSetting;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use App\Interfaces\MainSettingInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class MainSettingRepository implements MainSettingInterface
{
    private MainSetting $model;

    public function __construct(MainSetting $model)
    {
        $this->model = $model;
    }

    /**
     * Get a setting value by name
     *
     * @param string $name
     * @return string|null
     */
    public function get(string $name): ?string
    {
        $setting = $this->model::where('name', $name)->first();
        return $setting?->value;
    }

    /**
     * Set a setting value by name
     *
     * @param string $name
     * @param mixed $value
     * @return bool
     */
    public function set(string $name, $value): bool
    {
        $setting = $this->model::where('name', $name)->first();

        if ($setting) {
            $setting->value = $value;
            return $setting->save();
        }

        $setting = new $this->model;
        $setting->name = $name;
        $setting->value = $value;
        return $setting->save();
    }

    /**
     * Get all main settings
     *
     * @return Collection
     */
    public function all(): Collection
    {
        return $this->model::all();
    }

    /**
     * Update general settings including file uploads
     *
     * @param mixed $request
     * @return bool
     */
    public function updateGeneralSettings($request): bool
    {
        try {
            // Application name
            if ($request->has('application_name')) {
                $this->set('application_name', $request->application_name);
            }

            // Footer text
            if ($request->has('footer_text')) {
                $this->set('footer_text', $request->footer_text);
            }

            // Support email
            if ($request->has('support_email')) {
                $this->set('support_email', $request->support_email);
            }

            // Support phone
            if ($request->has('support_phone')) {
                $this->set('support_phone', $request->support_phone);
            }

            // Address
            if ($request->has('address')) {
                $this->set('address', $request->address);
            }

            // Country
            if ($request->has('country')) {
                $this->set('country', $request->country);
            }

            // Timezone
            if ($request->has('timezone')) {
                $this->set('timezone', $request->timezone);
            }

            // Phone
            if ($request->has('phone')) {
                $this->set('phone', $request->phone);
            }

            // Email
            if ($request->has('email')) {
                $this->set('email', $request->email);
            }

            // Default language
            if ($request->has('default_language')) {
                $this->set('default_language', $request->default_language);
                $path = base_path('lang/' . $request->default_language);
                if (is_dir($path)) {
                    session()->put('locale', $request->default_language);
                }
            }

            // Currency code
            if ($request->has('currency_code')) {
                $this->set('currency_code', $request->currency_code);
            }

            // Light logo upload
            if ($request->hasFile('light_logo') && $request->file('light_logo')->isValid()) {
                $filePath = $this->uploadFile($request->file('light_logo'), 'light_logo');
                if ($filePath) {
                    $this->set('light_logo', $filePath);
                }
            }

            // Dark logo upload
            if ($request->hasFile('dark_logo') && $request->file('dark_logo')->isValid()) {
                $filePath = $this->uploadFile($request->file('dark_logo'), 'dark_logo');
                if ($filePath) {
                    $this->set('dark_logo', $filePath);
                }
            }

            // Favicon upload
            if ($request->hasFile('favicon') && $request->file('favicon')->isValid()) {
                $filePath = $this->uploadFile($request->file('favicon'), 'favicon');
                if ($filePath) {
                    $this->set('favicon', $filePath);
                }
            }

            // Clear cache
            Cache::forget('main_settings');

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Upload a file and return its path
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $type
     * @return string|null
     */
    private function uploadFile($file, string $type): ?string
    {
        try {
            $path = 'backend/uploads/settings';
            $extension = $file->guessExtension();
            $filename = $type . '_' . Str::random(6) . '_' . time() . '.' . $extension;

            // Check if S3 storage is configured
            if (function_exists('setting') && setting('file_system') == 's3') {
                Storage::disk('s3')->put($path . '/' . $filename, file_get_contents($file));
                return Storage::disk('s3')->url($path . '/' . $filename);
            }

            // Local storage
            $file->move(public_path($path), $filename);
            return $path . '/' . $filename;
        } catch (\Throwable $th) {
            return null;
        }
    }

    /**
     * Get all settings as key-value array
     *
     * @return array
     */
    public function getAllAsArray(): array
    {
        return $this->model::all()->pluck('value', 'name')->toArray();
    }
}
