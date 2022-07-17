<?php

namespace App\Controller;

use App\Entity\Entity;
use App\Exception\JsonNotFoundException;
use App\Repository\Repository;
use App\Request\Request as CustomRequest;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends AbstractController
{
    public function __construct(
        private Repository $repository,
        private CacheItemPoolInterface $cacheItemPoolInterface
    ) {
    }

    abstract protected function createEntityObject(CustomRequest $request): Entity;

    abstract protected function updateEntityObject(Entity $entity, CustomRequest $request): Entity;

    abstract protected function checkEntityOnRequest(CustomRequest $request, bool $throwException = true): bool;

    abstract protected function cachePrefix(): string;

    private function getAllEntities()
    {
        $request = CustomRequest::createRequest();

        $extractor = $request->extractor;

        $filter = $extractor->extractFilter();
        $sort = $extractor->extractSort();
        $limit = $extractor->extractLimit();
        $page = $extractor->extractPage();

        $offset = ($page - 1) * $limit;

        $entities = $this->repository->findBy($filter, $sort, $limit, $offset);

        return $entities;
    }

    private function viewIndex()
    {
        $request = CustomRequest::createRequest();
        $extractor = $request->extractor;

        $limit = $extractor->extractLimit();
        $page = $extractor->extractPage();

        $entities = $this->getAllEntities();

        if (empty($entities)) {
            throw new JsonNotFoundException();
        }

        $view = [
            'current_page' => $page,
            'per_page' => $limit,
            'data' => $entities,
        ];

        return $view;
    }

    public function index(Request $request): JsonResponse
    {
        $view = $this->viewIndex();

        return new JsonResponse($view);
    }

    public function store(Request $request): JsonResponse
    {
        $request = CustomRequest::createRequest();

        $this->checkEntityOnRequest($request);

        $entity = $this->createEntityObject($request);

        $this->repository->add($entity, true);

        return new JsonResponse($entity, Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $entity = $this->repository->find($id);

        if (is_null($entity)) {
            throw new JsonNotFoundException();
        }

        return new JsonResponse($entity);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request = CustomRequest::createRequest();

        $this->checkEntityOnRequest($request);

        $entityFounded = $this->repository->find($id);

        if (is_null($entityFounded)) {
            throw new JsonNotFoundException();
        }

        $entityUpdated = $this->updateEntityObject($entityFounded, $request);

        return new JsonResponse($entityUpdated);
    }

    public function destroy(int $id): JsonResponse
    {
        $entity = $this->repository->find($id);

        if (is_null($entity)) {
            throw new JsonNotFoundException();
        }

        $this->repository->remove($entity, true);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
