<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class RequestCheckerService
{
    public function __construct(private readonly ValidatorInterface $validator)
    {
    }

    /**
     * Checks that request body exists and required fields are present.
     *
     * @param mixed $content decoded JSON array
     * @param string[] $fields required keys
     */
    public function check(mixed $content, array $fields): void
    {
        if (!isset($content) || !is_array($content)) {
            throw new BadRequestException('Empty content', Response::HTTP_BAD_REQUEST);
        }

        $missing = [];
        foreach ($fields as $field) {
            if (!array_key_exists($field, $content) || $content[$field] === null || $content[$field] === '') {
                $missing[] = $field;
            }
        }

        if ($missing) {
            throw new BadRequestException('Required fields are missed: ' . implode('; ', $missing), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Validate by Symfony constraints (attributes on Entity) or Collection constraints.
     *
     * @param array|object $data
     * @param array|null $constraints
     * @param bool $removeSquareBracketFromPropertyPath
     */
    public function validateRequestDataByConstraints(array|object $data, ?array $constraints = null, bool $removeSquareBracketFromPropertyPath = false): void
    {
        $errors = $this->validator->validate($data, !empty($constraints) ? new Collection($constraints) : null);

        if (count($errors) === 0) {
            return;
        }

        $validationErrors = [];
        foreach ($errors as $error) {
            $key = $error->getPropertyPath();
            if ($removeSquareBracketFromPropertyPath) {
                $key = preg_replace('/\[.*?\]/', '', $key);
            }
            $key = str_replace(['[', ']'], ['', ''], $key);
            $validationErrors[$key ?: 'global'] = $error->getMessage();
        }

        throw new UnprocessableEntityHttpException(json_encode($validationErrors, JSON_UNESCAPED_UNICODE));
    }
}
