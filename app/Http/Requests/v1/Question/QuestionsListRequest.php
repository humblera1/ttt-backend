<?php

namespace App\Http\Requests\v1\Question;

use App\DTOs\v1\Question\QuestionsFilterDTO;
use App\Http\Requests\BaseFormRequest;
use App\Interfaces\v1\Requests\RequestDTOInterface;
use App\Models\Company;
use App\Models\Question;
use Illuminate\Validation\Rule;

class QuestionsListRequest extends BaseFormRequest implements RequestDTOInterface
{
    public const array ALLOWED_SORTS = [
        'rating',
        '-rating',
        'published_at',
        '-published_at',
        'title',
        '-title',
    ];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user && $user->can('viewAny', Question::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['string', 'max:255'],
            'tagIds' => ['sometimes', 'array'],
            'tagIds.*'   => ['integer'],
            'gradeIds' => ['sometimes', 'array'],
            'gradeIds.*' => ['integer'],
            'companyIds' => [
                $this->user()->can('viewAny', Company::class) ? 'sometimes' : 'prohibited',
                'array'
            ],
            'companyIds.*' => ['integer'],
            'sort' => [
                'nullable',
                'string',
                Rule::in(self::ALLOWED_SORTS),
            ],
        ];
    }

    public function getDTO(): QuestionsFilterDTO
    {
        return new QuestionsFilterDTO(
            title: $this->validated('title'),
            sort: $this->validated('sort', '-rating'),
            tagIds: $this->validated('tagIds'),
            gradeIds: $this->validated('gradeIds'),
            companyIds: $this->validated('companyIds'),
        );
    }
}
