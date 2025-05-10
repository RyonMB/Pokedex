<?php

namespace App\Http\Requests;

use App\Models\Pokemon;
use App\Traits\FilterRequestTrait;
use Illuminate\Foundation\Http\FormRequest;

class PokemonSearchRequest extends FormRequest
{
    use FilterRequestTrait;
    
    /**
     * The model class to use for filter validation.
     * This property is required when using FilterRequestTrait.
     */
    protected $modelClass = Pokemon::class;
    
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
        return array_merge([
            // Add any additional request validation rules here
        ], $this->getFilterRules());
    }
    
    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return array_merge(parent::messages(), $this->getFilterMessages());
    }
    
    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return array_merge(parent::attributes(), $this->getFilterAttributes());
    }
}
