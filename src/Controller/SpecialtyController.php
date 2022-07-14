<?php

namespace App\Controller;

use App\Factory\SpecialtyFactory;
use App\Repository\SpecialtyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SpecialtyController extends AbstractController
{
    public function __construct(
        private SpecialtyRepository $specialtyRepository,
        private SpecialtyFactory $specialtyFactory
    ) {
    }

    private function jsonResponseNotFound(): JsonResponse
    {
        $error = ['Error' => 'No Resources Found'];
        $statusCode = Response::HTTP_NOT_FOUND;

        return new JsonResponse($error, $statusCode);
    }

    private function jsonResponseMissingParameters(): JsonResponse
    {
        $error = ['Error' => 'This Resource is Missing Parameters'];
        $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;

        return new JsonResponse($error, $statusCode);
    }

    #[Route(path: '/specialties', name: 'specialties.index', methods: 'GET')]
    public function index(): JsonResponse
    {
        return new JsonResponse([
            'Path' => '/specialties',
            'Name' => 'Specialties.Index',
            'Methods' => 'GET',
        ]);
    }

    #[Route(path: '/specialties', name: 'specialties.store', methods: 'POST')]
    public function store(Request $request): JsonResponse
    {
        $data = $request->toArray();

        $checks = $this->specialtyFactory->checkArrayToCreateSpecialty($data);

        if (!$checks) {
            return $this->jsonResponseMissingParameters();
        }

        $specialty = $this->specialtyFactory->createSpecialty($data);

        $this->specialtyRepository->add($specialty, true);

        return new JsonResponse($specialty);
    }

    #[Route(path: '/specialties/{id}', name: 'specialties.show', methods: 'GET')]
    public function show(): JsonResponse
    {
        return new JsonResponse([
            'Path' => '/specialties/{id}',
            'Name' => 'Specialties.Show',
            'Methods' => 'GET',
        ]);
    }

    #[Route(path: '/specialties/{id}', name: 'specialties.update', methods: 'PUT')]
    public function update(): JsonResponse
    {
        return new JsonResponse([
            'Path' => '/specialties/{id}',
            'Name' => 'Specialties.Update',
            'Methods' => 'PUT',
        ]);
    }

    #[Route(path: '/specialties/{id}', name: 'specialties.destroy', methods: 'DELETE')]
    public function destroy(): JsonResponse
    {
        return new JsonResponse([
            'Path' => '/specialties/{id}',
            'Name' => 'Specialties.Destroy',
            'Methods' => 'DELETE',
        ]);
    }
}
