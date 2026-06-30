<?php

namespace App\Support;

class DigitNormalizer
{
    private const PERSIAN = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];

    private const ARABIC = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];

    private const WESTERN = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    public static function toWestern(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }

        return str_replace(self::PERSIAN, self::WESTERN, str_replace(self::ARABIC, self::WESTERN, $value));
    }

    public static function normalizeArray(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = self::toWestern($value);
            } elseif (is_array($value)) {
                $data[$key] = self::normalizeArray($value);
            }
        }

        return $data;
    }
}
