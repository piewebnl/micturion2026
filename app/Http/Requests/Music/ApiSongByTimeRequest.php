<?php

namespace App\Http\Requests\Music;

use Illuminate\Foundation\Http\FormRequest;

class ApiSongByTimeRequest extends FormRequest
{
    public function rules()
    {
        return [
            'track_number' => 'required|integer',
            'time' => 'required|integer',
        ];
    }

    public function validationData()
    {
        return array_merge($this->all(), $this->route()->parameters());
    }
}
