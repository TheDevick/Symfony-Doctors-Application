<?php

namespace App\Controller;

use App\Entity\Entity;
use App\Repository\Repository;
use App\Request\Request as CustomRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends AbstractController
{
    public function __construct(
        private Repository $repository
    ) {
    }

    abstract protected function jsonResponseNotFound(bool $mainEntity = true): JsonResponse;

    abstract protected function createEntityObject(CustomRequest $request): Entity;

    abstract protected function updateEntityObject(Entity $entity, CustomRequest $request): Entity;

    abstract protected function checkEntityOnRequest(CustomRequest $request): bool;

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

    private function getFilterOnRequest(CustomRequest $request): array
    {
        $filterParameter = $request->getParameterBody('Filter', false);

        if (is_array($filterParameter)) {
            return array_change_key_case($filterParameter, CASE_LOWER);
        }

        return [];
    }

    public function index(Request $request): JsonResponse
    {
        $request = CustomRequest::createRequest();

        $sort = $this->getSortOnRequest($request);

        $filter = $this->getFilterOnRequest($request);

        $entities = $this->repository->findBy($filter, $sort);

        return new JsonResponse($entities);
    }

    public function store(Request $request): JsonResponse
    {
        $request = CustomRequest::createRequest();

        $checkEntityOnRequest = $this->checkEntityOnRequest($request);

        if (!$checkEntityOnRequest) {
            $message = ['Error' => 'This Resource is Missing Parameters'];
            $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;

            return new JsonResponse($message, $statusCode);
        }

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
        $request = CustomRequest::createRequest();

        $checkEntityOnRequest = $this->checkEntityOnRequest($request);

        if (!$checkEntityOnRequest) {
            $message = ['Error' => 'This Resource is Missing Parameters'];
            $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;

            return new JsonResponse($message, $statusCode);
        }

        $entityFounded = $this->repository->find($id);

        if (is_null($entityFounded)) {
            return $this->jsonResponseNotFound();
        }

        $entityUpdated = $this->updateEntityObject($entityFounded, $request);

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
