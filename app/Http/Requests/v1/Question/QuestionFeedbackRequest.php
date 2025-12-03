<?php

namespace App\Http\Requests\v1\Question;

use App\DTOs\v1\Question\QuestionFeedbackDTO;
use App\Enums\Period;
use App\Http\Requests\BaseFormRequest;
use App\Interfaces\v1\Requests\RequestDTOInterface;
use App\Models\Question;
use Illuminate\Validation\Rule;

class QuestionFeedbackRequest extends BaseFormRequest implements RequestDTOInterface
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user && $user->can('sendFeedback', Question::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'metInRealInterview' => ['required', 'boolean'],
            'whenAsked' => [
                'sometimes',
                'nullable',
                Rule::enum(Period::class)
            ],

            // Company
            'companyExisting' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:companies,id'
            ],
            'companyNew' => [
                'sometimes',
                'nullable',
                'string',
                'min:1',
                'max:255'
            ],

            // Position
            'positionExisting' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:positions,id'
            ],
            'positionNew' => [
                'sometimes',
                'nullable',
                'string',
                'min:1',
                'max:255'
            ],
        ];
    }

    public function getDTO(): QuestionFeedbackDTO
    {
        return new QuestionFeedbackDTO(
            metInRealInterview: $this->validated('metInRealInterview'),
            whenAsked: $this->validated('whenAsked'),
            companyExisting: $this->validated('companyExisting'),
            companyNew: $this->validated('companyNew'),
            positionExisting: $this->validated('positionExisting'),
            positionNew: $this->validated('positionNew'),
        );
    }
}
