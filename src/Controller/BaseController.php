<?php

namespace App\Controller;

use App\Factory\Factory;
use App\Repository\Repository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

abstract class BaseController extends AbstractController
{
    public function __construct(
        private Repository $repository,
        private Factory $factory
    ) {
    }

    abstract protected function checkStore(array $data): bool|JsonResponse;
    abstract protected function jsonResponseNotFound(): JsonResponse;

    public function index(): JsonResponse
    {
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

        return new JsonResponse($entity);
    }

    public function show(int $id): JsonResponse
    {
        $entity = $this->repository->find($id);

        if (is_null($entity)) {
            return $this->jsonResponseNotFound();
        }

        return new JsonResponse($entity);
    }
}
