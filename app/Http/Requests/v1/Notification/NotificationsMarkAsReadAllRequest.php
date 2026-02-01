<?php

namespace App\Http\Requests\v1\Notification;

use App\Models\UserNotification;
use Illuminate\Foundation\Http\FormRequest;

class NotificationsMarkAsReadAllRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth()->user();

        return $user && $user->can('markAsReadAll', UserNotification::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }
}
