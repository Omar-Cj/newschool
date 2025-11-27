<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * MainSetting Model
 *
 * IMPORTANT: This model extends Eloquent Model directly (NOT BaseModel)
 * to bypass SchoolScope. MainSettings are global system-wide settings
 * that should not be scoped to any specific school.
 */
class MainSetting extends Model
{
    protected $table = 'main_settings';

    protected $guarded = ['id'];

    /**
     * Clean up value attribute by removing escaped slashes
     *
     * @param string|null $value
     * @return string|null
     */
    public function getValueAttribute($value)
    {
        if ($value === null) {
            return null;
        }

        return str_replace('\\/', '/', $value);
    }

    /**
     * Get a setting value by name
     *
     * @param string $name
     * @return string|null
     */
    public static function getValue(string $name): ?string
    {
        $setting = static::where('name', $name)->first();
        return $setting?->value;
    }

    /**
     * Set a setting value by name
     *
     * @param string $name
     * @param mixed $value
     * @return bool
     */
    public static function setValue(string $name, $value): bool
    {
        $setting = static::where('name', $name)->first();

        if ($setting) {
            $setting->value = $value;
            return $setting->save();
        }

        $setting = new static();
        $setting->name = $name;
        $setting->value = $value;
        return $setting->save();
    }
}
