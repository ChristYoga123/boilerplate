<?php

namespace App\Support;

use Illuminate\Support\Str;
use Illuminate\View\ComponentAttributeBag;

class AdminFormField
{
    private const GLOBAL_ATTRIBUTES = [
        'id',
        'name',
        'label',
        'hint',
        'required',
        'disabled',
        'readonly',
        'value',
        'wrapper-class',
        'wrapperClass',
        'error-name',
        'errorName',
    ];

    public function __construct(
        public readonly ComponentAttributeBag $attributes,
        public readonly ?string $name,
        public readonly string $id,
        public readonly ?string $label,
        public readonly ?string $hint,
        public readonly mixed $value,
        public readonly bool $required,
        public readonly bool $disabled,
        public readonly bool $readonly,
        public readonly string $errorName,
        public readonly string $wrapperClass,
        public readonly array $errorNames,
    ) {}

    public static function make(ComponentAttributeBag $attributes, array $overrides = []): self
    {
        $name = $overrides['name'] ?? $attributes->get('name');
        $baseErrorName = $overrides['errorName']
            ?? $attributes->get('error-name')
            ?? $attributes->get('errorName')
            ?? self::normalizeErrorName($name);

        $extraErrorNames = $overrides['errorNames'] ?? [];
        $errorNames = array_values(array_unique(array_filter([
            $baseErrorName,
            ...$extraErrorNames,
        ])));

        return new self(
            attributes: $attributes,
            name: $name,
            id: $overrides['id'] ?? $attributes->get('id', self::idFromName($name)),
            label: $overrides['label'] ?? $attributes->get('label'),
            hint: $overrides['hint'] ?? $attributes->get('hint'),
            value: array_key_exists('value', $overrides) ? $overrides['value'] : $attributes->get('value'),
            required: self::toBool($overrides['required'] ?? $attributes->get('required', false)),
            disabled: self::toBool($overrides['disabled'] ?? $attributes->get('disabled', false)),
            readonly: self::toBool($overrides['readonly'] ?? $attributes->get('readonly', false)),
            errorName: $baseErrorName,
            wrapperClass: $overrides['wrapperClass']
                ?? $attributes->get('wrapper-class')
                ?? $attributes->get('wrapperClass')
                ?? 'mb-3',
            errorNames: $errorNames,
        );
    }

    public function controlAttributes(array $except = []): ComponentAttributeBag
    {
        return $this->attributes->except([
            ...self::GLOBAL_ATTRIBUTES,
            ...$except,
        ]);
    }

    public function oldValue(mixed $default = null): mixed
    {
        if ($this->name === null) {
            return $this->value ?? $default;
        }

        return old($this->errorName, $this->value ?? $default);
    }

    public function hasError(mixed $errors): bool
    {
        if (! is_object($errors) || ! method_exists($errors, 'has')) {
            return false;
        }

        foreach ($this->errorNames as $errorName) {
            if ($errors->has($errorName)) {
                return true;
            }
        }

        return false;
    }

    private static function normalizeErrorName(?string $name): string
    {
        return str_replace('[]', '', (string) $name);
    }

    private static function idFromName(?string $name): string
    {
        $name = self::normalizeErrorName($name);

        return Str::of($name)
            ->replaceMatches('/[^A-Za-z0-9\-_:.]+/', '-')
            ->trim('-')
            ->toString();
    }

    private static function toBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if ($value === null || $value === false) {
            return false;
        }

        if (is_string($value)) {
            return ! in_array(strtolower($value), ['', 'false', '0', 'no', 'off'], true);
        }

        return (bool) $value;
    }
}
