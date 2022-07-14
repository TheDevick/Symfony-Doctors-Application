<?php

namespace App\Controller;

use App\Entity\Entity;
use App\Entity\Specialty;
use App\Factory\SpecialtyFactory;
use App\Repository\DoctorRepository;
use App\Repository\SpecialtyRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

    protected function getEntityElements(): array
    {
        return ['Id', 'Title', 'Doctors', 'Description'];
    }

    protected function jsonResponseNotFound(bool $mainEntity = true): JsonResponse
    {
        $entity = $mainEntity ? 'Specialty' : 'Doctor';

        $error = ['Error' => $entity.' Not Found'];

        $statusCode = Response::HTTP_NOT_FOUND;

        return new JsonResponse($error, $statusCode);
    }

    protected function checkValueCreateSpecialty(string $value)
    {
        $value = strtolower($value);

        $elementsToCreateEntity = Specialty::ELEMENTS_TO_CREATE_ENTITY;

        $check = in_array($value, $elementsToCreateEntity);

        return $check;
    }

    protected function checkRequestCreateSpecialty(Request $request): bool|JsonResponse
    {
        $specialtyValues = $request->toArray();

        foreach ($specialtyValues as $specialtyValue => $specialtyElement) {
            $check = $this->checkValueCreateSpecialty($specialtyValue);

            if (!$check) {
                return false;
            }
        }

        return true;
    }

    protected function checkStore(Request $request): bool|JsonResponse
    {
        $checkRequest = $this->checkRequestCreateSpecialty($request);

        if (!$checkRequest) {
            $message = ['Error' => 'This Resource is Missing Parameters'];
            $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;

            return new JsonResponse($message, $statusCode);
        }

        return true;
    }

    protected function getSpecialtyValues(Request $request)
    {
        return [
            'Title' => $this->getParameterInBody($request, 'Title'),
            'Description' => $this->getParameterInBody($request, 'Description'),
        ];
    }

    protected function createEntityObject(Request $request): Entity
    {
        $values = $this->getSpecialtyValues($request);

        $specialty = new Specialty();

        $specialty->setTitle($values['Title']);
        $specialty->setDescription($values['Description']);

        return $specialty;
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
