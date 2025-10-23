<?php

namespace App\Http\Requests\v1\Question;

use App\DTOs\v1\Question\QuestionProposalDTO;
use App\Enums\Period;
use App\Http\Requests\BaseFormRequest;
use App\Interfaces\v1\Requests\RequestDTOInterface;
use App\Models\Question;
use Illuminate\Validation\Rule;

class QuestionProposalRequest extends BaseFormRequest implements RequestDTOInterface
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
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
            'interview.whenAsked' => [
                'sometimes',
                'nullable',
                Rule::enum(Period::class)
            ],

            // Company
            'interview.companyExisting' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:companies,id'
            ],
            'interview.companyNew' => [
                'sometimes',
                'nullable',
                'string',
                'min:1',
                'max:255'
            ],

            // Position
            'interview.positionExisting' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:positions,id'
            ],
            'interview.positionNew' => [
                'sometimes',
                'nullable',
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
            whenAsked: $this->validated('interview.whenAsked'),

            companyExisting: $this->validated('interview.companyExisting'),
            companyNew: $this->validated('interview.companyNew'),

            positionExisting: $this->validated('interview.positionExisting'),
            positionNew: $this->validated('interview.positionNew'),
        );
    }
}
