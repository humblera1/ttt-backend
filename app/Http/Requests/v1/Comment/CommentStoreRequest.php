<?php

namespace App\Http\Requests\v1\Comment;

use App\DTOs\v1\Comment\CommentStoreDTO;
use App\Interfaces\v1\Requests\RequestDTOInterface;
use App\Models\Comment;
use Illuminate\Foundation\Http\FormRequest;

class CommentStoreRequest extends FormRequest implements RequestDTOInterface
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
         return $this->user()->can('create', Comment::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'min:1', 'max:10000'],
            'parentId' => ['nullable', 'integer', 'exists:comments,id'],
        ];
    }

    public function getDTO(): CommentStoreDTO
    {
        return new CommentStoreDTO(
            user: $this->user(),
            body: $this->input('body'),
            parentId: $this->input('parentId'),
        );
    }
}
