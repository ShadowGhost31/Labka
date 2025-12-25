<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;

final class RuntimeConstraintExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $code = $this->getCode($exception);
        $errors = $this->getErrors($exception);

        $event->setResponse(new JsonResponse([
            'data' => [
                'code' => $code,
                'errors' => $errors,
            ],
        ], $code));
    }

    private function getCode(Throwable $exception): int
    {
        if (method_exists($exception, 'getStatusCode')) {
            $status = (int) $exception->getStatusCode();
            return array_key_exists($status, Response::$statusTexts) ? $status : Response::HTTP_UNPROCESSABLE_ENTITY;
        }

        $code = (int) $exception->getCode();
        return array_key_exists($code, Response::$statusTexts) ? $code : Response::HTTP_UNPROCESSABLE_ENTITY;
    }

    private function getErrors(Throwable $exception): array
    {
        // 1) Symfony validator exceptions that expose violations
        if (method_exists($exception, 'getConstraintViolationList')) {
            /** @var ConstraintViolationListInterface $list */
            $list = $exception->getConstraintViolationList();
            return $this->errorsFromViolationList($list);
        }

        // 2) If message is JSON (our services may throw JSON-encoded errors)
        $decoded = json_decode($exception->getMessage(), true);
        if (is_array($decoded)) {
            // could be ["field" => "message"] or {"data":{"errors":...}}
            $tmp = $decoded['data']['errors'] ?? $decoded;
            if (is_array($tmp)) {
                return $tmp;
            }
        }

        // 3) fallback
        return ['message' => $exception->getMessage()];
    }

    private function errorsFromViolationList(ConstraintViolationListInterface $list): array
    {
        $errors = [];
        foreach ($list as $violation) {
            $path = $violation->getPropertyPath() ?: 'global';
            $errors[$path] = $violation->getMessage();
        }
        return $errors;
    }
}
