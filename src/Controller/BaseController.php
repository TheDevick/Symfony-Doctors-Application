<?php

namespace App\Controller;

use App\Entity\Entity;
use App\Factory\Factory;
use App\Repository\Repository;
use App\Request\Request as CustomRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends AbstractController
{
    public function __construct(
        private Repository $repository,
        private Factory $factory
    ) {
    }

    abstract protected function jsonResponseNotFound(bool $mainEntity = true): JsonResponse;

    abstract protected function createEntityObject(CustomRequest $request): Entity;

    abstract protected function checkStore(CustomRequest $request): bool;

    public function index(Request $request): JsonResponse
    {
        $entities = $this->repository->findAll();

        return new JsonResponse($entities);
    }

    public function store(Request $request): JsonResponse
    {
        $request = CustomRequest::createRequest();

        $checkStore = $this->checkStore($request);

        $entity = $this->createEntityObject($request);

        $this->repository->add($entity, true);

        return new JsonResponse($entity, Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $entity = $this->repository->find($id);

        if (is_null($entity)) {
            return $this->jsonResponseNotFound();
        }

        return new JsonResponse($entity);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $entityFounded = $this->repository->find($id);

        if (is_null($entityFounded)) {
            return $this->jsonResponseNotFound();
        }

        $data = $request->toArray();

        $entityUpdated = $this->factory->updateEntity($entityFounded, $data);

        if (false == $entityUpdated) {
            return $this->jsonResponseNotFound(false);
        }

        return new JsonResponse($entityUpdated);
    }

    public function destroy(int $id): JsonResponse
    {
        $entity = $this->repository->find($id);

        if (is_null($entity)) {
            return $this->jsonResponseNotFound();
        }

        $this->repository->remove($entity, true);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
