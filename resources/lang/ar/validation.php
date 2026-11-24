<?php

/**
 * Core validation messages (Arabic).
 *
 * Fallback messages for rules that are not overridden by
 * `BasePortfolioFormRequest::messages()`. Attribute names come from
 * `portfolio_validation.attributes`.
 */

declare(strict_types=1);

return [

    'accepted' => 'يجب الموافقة على :attribute.',
    'array' => 'يجب أن يكون حقل :attribute مصفوفة صحيحة.',
    'between' => [
        'array' => 'يجب أن يحتوي :attribute على عدد عناصر بين :min و :max.',
        'file' => 'يجب أن يكون حجم ملف :attribute بين :min و :max كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة :attribute بين :min و :max.',
        'string' => 'يجب أن يكون طول :attribute بين :min و :max حرفاً.',
    ],
    'email' => 'يرجى إدخال بريد إلكتروني صحيح.',
    'enum' => 'القيمة المختارة في :attribute غير صالحة.',
    'exists' => 'القيمة المختارة في :attribute غير موجودة.',
    'file' => 'يجب أن يكون :attribute ملفاً.',
    'in' => 'القيمة المختارة في :attribute غير صالحة.',
    'integer' => 'يجب أن يكون حقل :attribute رقماً صحيحاً.',
    'max' => [
        'array' => 'يجب ألا يحتوي :attribute على أكثر من :max عناصر.',
        'file' => 'يجب ألا يتجاوز حجم ملف :attribute :max كيلوبايت.',
        'numeric' => 'يجب ألا يزيد :attribute عن :max.',
        'string' => 'يجب ألا يتجاوز :attribute :max حرفاً.',
    ],
    'mimes' => 'يجب أن يكون ملف :attribute من نوع: :values.',
    'min' => [
        'array' => 'يجب أن يحتوي :attribute على :min عناصر على الأقل.',
        'file' => 'يجب ألا يقل حجم ملف :attribute عن :min كيلوبايت.',
        'numeric' => 'يجب ألا يقل :attribute عن :min.',
        'string' => 'يجب ألا يقل طول :attribute عن :min حروف.',
    ],
    'prohibited' => 'غير مسموح بإرسال حقل :attribute.',
    'required' => 'حقل :attribute مطلوب.',
    'string' => 'يجب أن يكون حقل :attribute نصاً صحيحاً.',

];
