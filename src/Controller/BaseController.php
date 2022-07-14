<?php

namespace App\Controller;

use App\Factory\Factory;
use App\Repository\Repository;
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

    abstract protected function checkStore(array $data): bool|JsonResponse;
    abstract protected function jsonResponseNotFound(bool $mainEntity = true): JsonResponse;

    protected function getParameterInBody(Request $request, string $parameter, $default = null)
    {
        if ($request->getContent() == null) {
            return false;
        }

        $allParameters = $request->toArray();

        if (!array_key_exists($parameter, $allParameters)) {
            return $default;
        }

        $value = $allParameters[$parameter];

        return $value;
    }

    protected function arrayKeysToLowerCase(array $array)
    {
        return array_change_key_case($array, CASE_LOWER);
    }

    protected function sortRequestFilter(Request $request): array
    {
        $defaultSortValue = ['id' => 'ASC'];

        $sortBody = $this->getParameterInBody($request, 'Sort', $defaultSortValue);
        $sortBodyType = gettype($sortBody);

        if ($sortBodyType == 'array') {
            $sort = $this->arrayKeysToLowerCase($sortBody);
        }

        if ($sortBodyType != 'array' && $sortBodyType != 'string') {
            $sort = $defaultSortValue;
        }

        return $sort;
    }

    public function index(Request $request): JsonResponse
    {
        $sort = $this->sortRequestFilter($request);

        $entities = $this->repository->findBy([], orderBy: $sort);

        $entities = $this->repository->findAll();

        return new JsonResponse($entities);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->toArray();

        $check = $this->checkStore($data);

        if (is_object($check)) {
            return $check;
        }

        $entity = $this->factory->createEntity($data);

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
