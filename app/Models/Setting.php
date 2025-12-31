<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    protected $primaryKey = 'key';

    public static function addSetting(string $key, string $value)
    {

        $setting = Setting::firstOrNew(['key' => $key]);
        $setting->value = $value;
        $setting->save();

    }

    public static function getSetting(string $key)
    {

        $setting = Setting::where('key', $key)->value('value');

        return $setting;

    }
}
