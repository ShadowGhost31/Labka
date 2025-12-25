<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/items')]
class ItemController extends AbstractController
{
    private function getItems(SessionInterface $session): array
    {
        return $session->get('items', []);
    }

    private function saveItems(SessionInterface $session, array $items): void
    {
        $session->set('items', $items);
    }

    #[Route('', name: 'items_index', methods: ['GET'])]
    public function index(SessionInterface $session): Response
    {
        return $this->json(array_values($this->getItems($session)));
    }

    #[Route('', name: 'items_create', methods: ['POST'])]
    public function create(Request $request, SessionInterface $session): Response
    {
        $items = $this->getItems($session);

        $name = trim((string)$request->request->get('name', ''));
        if ($name === '') {
            return $this->json(['error' => 'name is required'], 400);
        }

        $id = (int) (count($items) ? max(array_map('intval', array_keys($items))) + 1 : 1);

        $items[(string)$id] = ['id' => $id, 'name' => $name];
        $this->saveItems($session, $items);

        return $this->json($items[(string)$id], 201);
    }

    #[Route('/{id}', name: 'items_update', requirements: ['id' => '\d+'], methods: ['PUT'])]
    public function update(int $id, Request $request, SessionInterface $session): Response
    {
        $items = $this->getItems($session);

        if (!isset($items[(string)$id])) {
            return $this->json(['error' => 'Not found'], 404);
        }

        $name = trim((string)$request->request->get('name', ''));
        if ($name === '') {
            return $this->json(['error' => 'name is required'], 400);
        }

        $items[(string)$id]['name'] = $name;
        $this->saveItems($session, $items);

        return $this->json($items[(string)$id]);
    }

    #[Route('/{id}', name: 'items_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(int $id, SessionInterface $session): Response
    {
        $items = $this->getItems($session);

        if (!isset($items[(string)$id])) {
            return $this->json(['error' => 'Not found'], 404);
        }

        unset($items[(string)$id]);
        $this->saveItems($session, $items);

        return $this->json(['status' => 'deleted']);
    }
}
