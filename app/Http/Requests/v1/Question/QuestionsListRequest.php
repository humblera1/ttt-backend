<?php

namespace App\Http\Requests\v1\Question;

use App\DTOs\v1\Question\QuestionsFilterDTO;
use App\Http\Requests\BaseFormRequest;
use App\Interfaces\v1\Requests\RequestDTOInterface;

class QuestionsListRequest extends BaseFormRequest implements RequestDTOInterface
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
        return [
            'title' => 'string|max:255',
            'tagIds' => 'array',
            'gradeIds' => 'array',
            'companyIds' => 'array',
        ];
    }

    public function getDTO(): QuestionsFilterDTO
    {
        return new QuestionsFilterDTO(
            title: $this->validated('title'),
            tagIds: $this->validated('tagIds'),
            gradeIds: $this->validated('gradeIds'),
            companyIds: $this->validated('name'),
        );
    }
}
