<?php

namespace App\Http\Requests\v1\Question;

use App\DTOs\v1\Question\QuestionProposalDTO;
use App\Http\Requests\BaseFormRequest;
use App\Interfaces\v1\Requests\RequestDTOInterface;
use App\Models\Question;

class QuestionProposalRequest extends BaseFormRequest implements RequestDTOInterface
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;

        $user = $this->user();

        return $user && $user->can('propose', Question::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Main Info
            'title' => ['required', 'string', 'max:255'],
            'answer' => ['sometimes', 'nullable', 'string'],
            'isAnonymous' => ['sometimes', 'boolean'],

            // Tags
            'tagsExisting' => ['sometimes', 'array'],
            'tagsExisting.*' => ['integer', 'distinct', 'exists:tags,id'],
            'tagsNew' => ['sometimes', 'array'],
            'tagsNew.*' => ['string', 'min:1', 'max:100'],

            // Grades
            'grades' => ['sometimes', 'array'],
            'grades.*' => ['integer', 'distinct', 'exists:grades,id'],

            // Interview
            'interview' => ['sometimes', 'array'],
            'interview.metInRealInterview' => ['required_with:interview', 'boolean'],

            // Company
            'interview.companyExisting' => [
                'sometimes',
                'nullable',
                'required_with:interview',
                'integer',
                'exists:companies,id'
            ],
            'interview.companyNew' => [
                'sometimes',
                'nullable',
                'required_with:interview',
                'string',
                'min:1',
                'max:255'
            ],

            // Position
            'interview.positionExisting' => [
                'sometimes',
                'nullable',
                'required_with:interview',
                'integer',
                'exists:positions,id'
            ],
            'interview.positionNew' => [
                'sometimes',
                'nullable',
                'required_with:interview',
                'string',
                'min:1',
                'max:255'
            ],
        ];
    }


    public function getDTO(): QuestionProposalDTO
    {
        return new QuestionProposalDTO(
            title: $this->validated('title'),
            isAnonymous: $this->validated('isAnonymous', false),
            answer: $this->validated('answer'),

            tagsExisting: $this->validated('tagsExisting', []),
            tagsNew: $this->validated('tagsNew', []),

            grades: $this->validated('grades', []),

            metInRealInterview: $this->validated('interview.metInRealInterview'),

            companyExisting: $this->validated('interview.companyExisting'),
            companyNew: $this->validated('interview.companyNew'),

            positionExisting: $this->validated('interview.positionExisting'),
            positionNew: $this->validated('interview.positionNew'),
        );
    }
}
