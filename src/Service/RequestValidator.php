<?php

namespace App\Service;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class RequestValidator
{
    /**
     * @param array<string, mixed> $data
     * @param string[] $required
     */
    public function requireFields(array $data, array $required): void
    {
        $missing = [];
        foreach ($required as $field) {
            if (!array_key_exists($field, $data) || $data[$field] === null || $data[$field] === '') {
                $missing[] = $field;
            }
        }
        if ($missing) {
            throw new BadRequestHttpException('Missing required fields: ' . implode(', ', $missing));
        }
    }

    public function requireInt(mixed $value, string $field): int
    {
        if ($value === null || $value === '') {
            throw new BadRequestHttpException("Field '$field' is required");
        }
        if (is_int($value)) return $value;
        if (is_string($value) && ctype_digit($value)) return (int) $value;
        throw new BadRequestHttpException("Field '$field' must be an integer");
    }

    public function requireNonNegativeInt(mixed $value, string $field): int
    {
        $v = $this->requireInt($value, $field);
        if ($v < 0) {
            throw new BadRequestHttpException("Field '$field' must be >= 0");
        }
        return $v;
    }

    public function requireString(mixed $value, string $field, int $minLen = 1, int $maxLen = 255): string
    {
        if (!is_string($value)) {
            throw new BadRequestHttpException("Field '$field' must be a string");
        }
        $s = trim($value);
        $len = mb_strlen($s);
        if ($len < $minLen) {
            throw new BadRequestHttpException("Field '$field' must be at least {$minLen} chars");
        }
        if ($len > $maxLen) {
            throw new BadRequestHttpException("Field '$field' must be at most {$maxLen} chars");
        }
        return $s;
    }

    public function optionalString(mixed $value, int $maxLen = 1000): ?string
    {
        if ($value === null) return null;
        if (!is_string($value)) return null;
        $s = trim($value);
        if ($s === '') return null;
        if (mb_strlen($s) > $maxLen) {
            $s = mb_substr($s, 0, $maxLen);
        }
        return $s;
    }

    public function requireEmail(mixed $value, string $field = 'email'): string
    {
        $email = $this->requireString($value, $field, 5, 180);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new BadRequestHttpException("Field '$field' must be a valid email");
        }
        return $email;
    }

    public function optionalHexColor(mixed $value, string $field = 'color'): ?string
    {
        if ($value === null || $value === '') return null;
        if (!is_string($value)) {
            throw new BadRequestHttpException("Field '$field' must be a string");
        }
        $c = trim($value);
        if (!preg_match('/^#?[0-9a-fA-F]{6}$/', $c)) {
            throw new BadRequestHttpException("Field '$field' must be a hex color like #AABBCC");
        }
        return str_starts_with($c, '#') ? $c : ('#' . $c);
    }

    public function optionalDateTimeImmutable(mixed $value, string $field): ?\DateTimeImmutable
    {
        if ($value === null || $value === '') return null;
        if (!is_string($value)) {
            throw new BadRequestHttpException("Field '$field' must be an ISO date string");
        }
        try {
            return new \DateTimeImmutable($value);
        } catch (\Throwable) {
            throw new BadRequestHttpException("Field '$field' has invalid date format");
        }
    }

    public function requireDateImmutable(mixed $value, string $field): \DateTimeImmutable
    {
        if (!is_string($value) || trim($value) === '') {
            throw new BadRequestHttpException("Field '$field' must be a date string (YYYY-MM-DD)");
        }
        try {
            // Force date-only
            return new \DateTimeImmutable(substr($value, 0, 10));
        } catch (\Throwable) {
            throw new BadRequestHttpException("Field '$field' has invalid date format");
        }
    }
}
