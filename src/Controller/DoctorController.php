<?php

namespace App\Controller;

use App\Factory\DoctorFactory;
use App\Repository\DoctorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DoctorController extends AbstractController
{
    public function __construct(
        private DoctorRepository $doctorRepository,
        private DoctorFactory $doctorFactory
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

    #[Route(path: '/doctors', name: 'doctors.index', methods: 'GET')]
    public function index(): JsonResponse
    {
        $doctors = $this->doctorRepository->findAll();

        return new JsonResponse($doctors);
    }

    #[Route(path: '/doctors', name: 'doctors.store', methods: 'POST')]
    public function store(Request $request): JsonResponse
    {
        $data = $request->toArray();

        $checks = $this->doctorFactory->checkArrayToCreateDoctor($data);

        if (!$checks) {
            return $this->jsonResponseMissingParameters();
        }

        $doctor = $this->doctorFactory->createDoctor($data);

        $this->doctorRepository->add($doctor, true);

        return new JsonResponse($doctor);
    }

    #[Route(path: '/doctors/{id}', name: 'doctors.show', methods: 'GET')]
    public function show(int $id): JsonResponse
    {
        $doctor = $this->doctorRepository->find($id);

        if (is_null($doctor)) {
            return $this->jsonResponseNotFound();
        }

        return new JsonResponse($doctor);
    }

    #[Route(path: '/doctors/{id}', name: 'doctors.update', methods: 'PUT')]
    public function update(Request $request, int $id): JsonResponse
    {
        $doctor = $this->doctorRepository->find($id);

        if (is_null($doctor)) {
            return $this->jsonResponseNotFound();
        }

        $data = $request->toArray();

        $this->doctorFactory->updateDoctor($doctor, $data);

        $this->doctorRepository->flush();

        return new JsonResponse($doctor);
    }

    #[Route(path: '/doctors/{id}', name: 'doctors.destroy', methods: 'DELETE')]
    public function destroy(): JsonResponse
    {
        return new JsonResponse([
            'Path' => '/doctors/{id}',
            'Name' => 'Doctors.Destroy',
            'Methods' => 'DELETE',
        ]);
    }
}
