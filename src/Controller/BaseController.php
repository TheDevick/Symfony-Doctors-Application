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

    abstract protected function createEntityObject(CustomRequest $request): Entity|false;

    abstract protected function checkStore(CustomRequest $request): bool;

    abstract protected function onCreateEntityError(): JsonResponse;

    private function getSortOnRequest(CustomRequest $request, array $default = null)
    {
        if (is_null($default)) {
            $default = ['id' => 'ASC'];
        }

        $sortParameter = $request->getParameterBody('Sort', $default);

        if (is_array($sortParameter)) {
            return array_change_key_case($sortParameter, CASE_LOWER);
        }

        return $default;
    }

    public function index(Request $request): JsonResponse
    {
        $request = CustomRequest::createRequest();

        $sort = $this->getSortOnRequest($request);

        $entities = $this->repository->findBy([], $sort);

        return new JsonResponse($entities);
    }

    public function store(Request $request): JsonResponse
    {
        $request = CustomRequest::createRequest();

        $checkStore = $this->checkStore($request);

        if (!$checkStore) {
            $message = ['Error' => 'This Resource is Missing Parameters'];
            $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;

            return new JsonResponse($message, $statusCode);
        }

        $entity = $this->createEntityObject($request);

        if (!$entity) {
            return $this->onCreateEntityError();
        }

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

    // protected function checkFilters($filters)
    // {
    //     if (!is_array($filters)) {
    //         return false;
    //     }

    //     foreach ($filters as $key => $filter) {
    //         $filterExistsOnEntityElements = in_array($key, $this->getEntityElements());

    //         if (!$filterExistsOnEntityElements) {
    //             return false;
    //         }
    //     }

    //     return true;
    // }

    // protected function getFiltersOnRequest(Request $request, bool $lowerCase = true): array
    // {
    //     $filters = $this->getParameterInBody($request, 'Filter');

    //     if (is_null($filters) || !$this->checkFilters($filters)) {
    //         return [];
    //     }

    //     if ($lowerCase) {
    //         $filters = $this->arrayKeysToLowerCase($filters);
    //     }

    //     return $filters;
    // }
}
