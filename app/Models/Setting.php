<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, $default = null)
    {
        $s = static::where('key', $key)->first();
        return $s ? $s->value : $default;
    }

    public static function set(string $key, $value)
    {
        $s = static::updateOrCreate(['key' => $key], ['value' => $value]);
        return $s;
    }

    /**
     * Get company name from settings
     */
    public static function getCompanyName()
    {
        return static::get('company_name', 'Vida Maria');
    }
}
