<?php

namespace App\Controller\Api;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class BaseApiController extends AbstractController
{
    protected function jsonOk(mixed $data, int $status = 200): JsonResponse
    {
        return $this->json($data, $status, [], [
            // Prevent circular refs when serializing relations
            'circular_reference_handler' => static fn (object $o) => method_exists($o, 'getId') ? $o->getId() : spl_object_id($o),
        ]);
    }

    protected function jsonError(string $message, int $status = 400, array $extra = []): JsonResponse
    {
        return $this->json(['error' => $message] + $extra, $status);
    }

    /**
     * Parse JSON body safely.
     * @return array<string, mixed>
     */
    protected function getJson(Request $request): array
    {
        $raw = (string) $request->getContent();
        if ($raw === '') {
            return [];
        }
        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }

    protected function flush(EntityManagerInterface $em): void
    {
        $em->flush();
    }
}
