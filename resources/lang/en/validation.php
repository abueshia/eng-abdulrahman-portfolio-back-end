<?php

/**
 * Core validation messages (English).
 *
 * Fallback messages for rules that are not overridden by
 * `BasePortfolioFormRequest::messages()`. Attribute names come from
 * `portfolio_validation.attributes`.
 */

declare(strict_types=1);

return [

    'accepted' => 'The :attribute must be accepted.',
    'array' => 'The :attribute must be a valid array.',
    'between' => [
        'array' => 'The :attribute must have between :min and :max items.',
        'file' => 'The :attribute must be between :min and :max kilobytes.',
        'numeric' => 'The :attribute must be between :min and :max.',
        'string' => 'The :attribute must be between :min and :max characters.',
    ],
    'email' => 'Please provide a valid email address.',
    'enum' => 'The selected :attribute value is not valid.',
    'exists' => 'The selected :attribute is invalid or does not exist.',
    'file' => 'The :attribute must be a file.',
    'in' => 'The selected :attribute value is not valid.',
    'integer' => 'The :attribute must be an integer.',
    'max' => [
        'array' => 'The :attribute may not have more than :max items.',
        'file' => 'The :attribute may not be larger than :max kilobytes.',
        'numeric' => 'The :attribute may not be greater than :max.',
        'string' => 'The :attribute may not be greater than :max characters.',
    ],
    'mimes' => 'The :attribute must be a file of type: :values.',
    'min' => [
        'array' => 'The :attribute must have at least :min items.',
        'file' => 'The :attribute must be at least :min kilobytes.',
        'numeric' => 'The :attribute must be at least :min.',
        'string' => 'The :attribute must be at least :min characters.',
    ],
    'prohibited' => 'The :attribute field is not allowed.',
    'required' => 'The :attribute field is required.',
    'string' => 'The :attribute must be a valid text string.',

];
