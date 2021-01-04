<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\Rule;
use App\Models\Link;

class UrlMonitorRequest extends FormRequest
{
    // protected array $queryParametersToValidate = [];

    public function rules(): array
    {
        return [
            'urls' => [
                'required',
                'array',
                'min:1'
            ],
            'urls.*' => [
                'url',
                'distinct:ignore_case',
                Rule::unique(Link::class, 'url'),
            ]
        ];
    }

    public function messages()
    {
        return [
            'urls.min' => 'You have to send at least one URL!',
            'urls.*.url' => 'URL :input is not valid',
            'urls.*.unique' => 'URL :input is exists!',
        ];
    }
}
