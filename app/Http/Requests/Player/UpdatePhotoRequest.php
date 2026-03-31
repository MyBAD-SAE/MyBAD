<?php

namespace App\Http\Requests\Player;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'photo.required' => 'Veuillez sélectionner une photo.',
            'photo.image'    => 'Le fichier doit être une image.',
            'photo.mimes'    => 'L\'image doit être au format JPG, PNG ou WebP.',
            'photo.max'      => 'L\'image ne doit pas dépasser 2 Mo.',
        ];
    }
}
