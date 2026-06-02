<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class FetchVideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'url' => ['required', 'url', 'max:2048'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $url = $this->string('url')->toString();

            if ($url === '') {
                return;
            }

            $host = parse_url($url, PHP_URL_HOST);

            if (! is_string($host) || ! $this->isAllowedHost($host)) {
                $validator->errors()->add(
                    'url',
                    __('vaultfetch.validation.unsupported_host'),
                );
            }
        });
    }

    private function isAllowedHost(string $host): bool
    {
        $host = strtolower($host);

        foreach (config('vaultfetch.allowed_hosts', []) as $allowed) {
            if ($host === strtolower($allowed)) {
                return true;
            }
        }

        return false;
    }
}
