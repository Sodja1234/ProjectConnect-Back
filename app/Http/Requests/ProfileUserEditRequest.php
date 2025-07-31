<?php

namespace App\Http\Requests;

use App\Models\Profile;
use Illuminate\Validation\Rules\Unique;

class ProfileUserEditRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $profileId = $this->input('id');

        $rules = [
            'phone' => [
                'nullable',
                'string',
                'max:20',
                (new Unique(Profile::class))->ignore($profileId)
            ],

            'job_title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'about' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'location' => [
                'nullable',
                'string',
                'max:255',
            ],

            'is_availability' => [
                'nullable',
            ],

        ];

        if ($this->hasFile('profile_photo')) {
            $rules['profile_photo'] = [
                'nullable',
                'image',
                'max:2048', // 2MB
            ];
        }

        return $rules;
    }
}
