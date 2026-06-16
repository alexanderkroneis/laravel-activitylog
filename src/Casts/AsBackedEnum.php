<?php

namespace Spatie\Activitylog\Casts;

use BackedEnum;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use TypeError;

class AsBackedEnum implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(
        Model $model,
        string $key,
        mixed $value,
        array $attributes,
    ): BackedEnum|string|null {
        $default = config('activitylog.default_log_enum');

        if ($value === null || ! is_string($default) || ! enum_exists($default) || ! is_subclass_of($default, BackedEnum::class)) {
            return $value;
        }

        try {
            // A value that doesn't belong to the enum (or, for int-backed
            // enums, a non-numeric value) falls back to the raw string.
            return $default::tryFrom($value) ?? $value;
        } catch (TypeError) {
            return $value;
        }
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(
        Model $model,
        string $key,
        mixed $value,
        array $attributes,
    ): BackedEnum|string|null {
        if ($value instanceof BackedEnum) {
            return $value->value;
        }

        return $value;
    }
}
