<?php

namespace App\Http\Requests\LastFmApi;

use Illuminate\Foundation\Http\FormRequest;

class LastFmScrobbleTrackRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => 'integer|required',
        ];
    }
}
