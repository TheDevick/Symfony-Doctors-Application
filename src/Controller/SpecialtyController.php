<?php

namespace App\Controller;

use App\Factory\SpecialtyFactory;
use App\Repository\DoctorRepository;
use App\Repository\SpecialtyRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SpecialtyController extends BaseController
{
    public function __construct(
        private SpecialtyRepository $specialtyRepository,
        private DoctorRepository $doctorRepository,
        private SpecialtyFactory $specialtyFactory
    ) {
        parent::__construct($specialtyRepository, $specialtyFactory);
    }

    protected function jsonResponseNotFound(bool $mainEntity = true): JsonResponse
    {
        if ($mainEntity) {
            $error = ['Error' => 'Specialty Not Found'];
        } else {
            $error = ['Error' => 'Doctor Not Found'];
        }

        $statusCode = Response::HTTP_NOT_FOUND;

        return new JsonResponse($error, $statusCode);
    }

    protected function checkStore(array $data): bool|JsonResponse
    {
        $checkArrayToCreateSpecialty = $this->specialtyFactory->checkArrayToCreateSpecialty($data);

        $error = $checkArrayToCreateSpecialty->error ?? true;

        $check = true === $error ? true : false;

        if (!$check) {
            $message = $error->message;
            $statusCode = $error->statusCode;

            return new JsonResponse($message, $statusCode);
        }

        return true;
    }

    #[Route(path: '/specialties/{id}', name: 'specialties.destroy', methods: 'DELETE')]
    public function destroy(int $id): JsonResponse
    {
        $specialty = $this->specialtyRepository->find($id);

        if (is_null($specialty)) {
            return $this->jsonResponseNotFound();
        }

        $this->specialtyRepository->remove($specialty, true);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route(path: '/specialties/{id}/doctors', name: 'specialties.showDoctors', methods: 'GET')]
    public function showDoctors(int $id)
    {
        $doctors = $this->doctorRepository->findBy(['specialty' => $id]);

        if (empty($doctors)) {
            return $this->jsonResponseNotFound();
        }

        return new JsonResponse($doctors);
    }
}
