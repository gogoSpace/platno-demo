<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StartDemoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, list<string>> */
    public function rules(): array
    {
        return ['template' => ['required', 'string', 'in:studio,story,product'], 'confirmed' => [$this->routeIs('demo.restart') ? 'accepted' : 'sometimes']];
    }
}
