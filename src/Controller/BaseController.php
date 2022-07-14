<?php

namespace App\Controller;

use App\Factory\Factory;
use App\Repository\Repository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class BaseController extends AbstractController
{
    public function __construct(
        private Repository $repository,
        private Factory $factory
    ) {
    }

    public function index(): JsonResponse
    {
        $entities = $this->repository->findAll();

        return new JsonResponse($entities);
    }
}
