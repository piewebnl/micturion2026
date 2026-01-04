<?php

namespace App\Http\Requests\Music;

use Illuminate\Foundation\Http\FormRequest;

class ApiSongWithAlbumRequest extends FormRequest
{
    public function rules()
    {
        return [
            'album_id' => 'required|integer',
        ];
    }

    public function validationData()
    {
        return array_merge($this->all(), $this->route()->parameters());
    }
}
