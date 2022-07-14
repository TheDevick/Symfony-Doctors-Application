<?php

namespace App\Controller;

use App\Factory\DoctorFactory;
use App\Repository\DoctorRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DoctorController extends BaseController
{
    public function __construct(
        private DoctorRepository $doctorRepository,
        private DoctorFactory $doctorFactory
    ) {
        parent::__construct($doctorRepository, $doctorFactory);
    }

    private function jsonResponseNotFound(): JsonResponse
    {
        $error = ['Error' => 'No Resources Found'];
        $statusCode = Response::HTTP_NOT_FOUND;

        return new JsonResponse($error, $statusCode);
    }

    protected function checkStore(array $data): bool|JsonResponse
    {
        $checkArrayToCreateDoctor = $this->doctorFactory->checkArrayToCreateDoctor($data);

        $error = $checkArrayToCreateDoctor->error ?? true;

        $check = true === $error ? true : false;

        if (!$check) {
            $message = $error->message;
            $statusCode = $error->statusCode;

            return new JsonResponse($message, $statusCode);
        }

        return true;
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
    public function destroy(int $id): JsonResponse
    {
        $doctor = $this->doctorRepository->find($id);

        if (is_null($doctor)) {
            return $this->jsonResponseNotFound();
        }

        $this->doctorRepository->remove($doctor, true);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
