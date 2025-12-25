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
        if (is_string($value) && ctype_digit($value)) return (int)$value;
        throw new BadRequestHttpException("Field '$field' must be an integer");
    }
}
