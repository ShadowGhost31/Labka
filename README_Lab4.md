# Лабораторна робота №4 — Пагінація та фільтрація 


---

##  Пагінація та фільтрація для Entity

Для **кожної Entity** реалізовано:
- пагінацію (`itemsPerPage`, `page`);
- фільтрацію за основними полями;
- повернення метаданих (`totalItems`, `totalPageCount`).

Пагінація та фільтрація реалізовані на рівні **Repository** з використанням `Doctrine\ORM\Tools\Paginator`.

---

##  Реалізація в Controller

У всіх контролерах метод отримання колекції приймає параметри з `QueryString`.

**Приклад Controller:**
```php
#[Route('/api/users', methods: ['GET'])]
public function index(Request $request): JsonResponse
{
    $requestData = $request->query->all();

    $itemsPerPage = isset($requestData['itemsPerPage'])
        ? (int)$requestData['itemsPerPage']
        : 10;

    $page = isset($requestData['page'])
        ? (int)$requestData['page']
        : 1;

    $data = $this->userRepository->getAllByFilter(
        $requestData,
        $itemsPerPage,
        $page
    );

    return new JsonResponse($data);
}
```

---

##  Реалізація в Repository

Для кожного Repository реалізовано метод типу `getAllByFilter`.

**Приклад Repository:**
```php
public function getAllByFilter(array $data, int $itemsPerPage, int $page): array
{
    $qb = $this->createQueryBuilder('entity');

    if (isset($data['name'])) {
        $qb->andWhere('entity.name LIKE :name')
           ->setParameter('name', '%' . $data['name'] . '%');
    }

    $paginator = new Paginator($qb);
    $totalItems = count($paginator);
    $totalPageCount = ceil($totalItems / $itemsPerPage);

    $qb->setFirstResult($itemsPerPage * ($page - 1))
       ->setMaxResults($itemsPerPage);

    return [
        'items' => $qb->getQuery()->getResult(),
        'totalItems' => $totalItems,
        'totalPageCount' => $totalPageCount
    ];
}
```

---

##  Приклади запитів

### Users (пагінація + фільтр)
```powershell
Invoke-RestMethod "http://localhost:8000/api/users?itemsPerPage=5&page=1&email=test"
```

### Projects (фільтр за назвою та власником)
```powershell
Invoke-RestMethod "http://localhost:8000/api/projects?itemsPerPage=10&page=1&title=Lab&ownerId=1"
```

### Tasks (фільтри за звʼязками)
```powershell
Invoke-RestMethod "http://localhost:8000/api/tasks?itemsPerPage=10&page=1&projectId=1&statusId=1"
```

### Time Entries
```powershell
Invoke-RestMethod "http://localhost:8000/api/time-entries?itemsPerPage=10&page=1&userId=1"
```

---

##  Формат відповіді API

```json
{
  "items": [...],
  "totalItems": 25,
  "totalPageCount": 3
}
```
