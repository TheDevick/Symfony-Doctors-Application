<?php

namespace App\Controller;

use App\Entity\Doctor;
use App\Entity\Specialty;
use App\Factory\DoctorFactory;
use App\Repository\DoctorRepository;
use App\Repository\SpecialtyRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DoctorController extends BaseController
{
    public function __construct(
        private SpecialtyRepository $specialtyRepository,
        private DoctorRepository $doctorRepository,
        private DoctorFactory $doctorFactory
    ) {
        parent::__construct($doctorRepository, $doctorFactory);
    }

    protected function getEntityElements(): array
    {
        return ['Id', 'Name', 'Area', 'Subscription', 'SpecialtyId'];
    }

    protected function jsonResponseNotFound(bool $mainEntity = true): JsonResponse
    {
        $entity = $mainEntity ? 'Doctor' : 'Specialty';

        $error = ['Error' => $entity.' Not Found'];

        $statusCode = Response::HTTP_NOT_FOUND;

        return new JsonResponse($error, $statusCode);
    }

    protected function checkValueCreateDoctor(string $value)
    {
        $value = strtolower($value);

        $elementsToCreateEntity = Doctor::ELEMENTS_TO_CREATE_ENTITY;

        $check = in_array($value, $elementsToCreateEntity);

        return $check;
    }

    protected function checkRequestCreateDoctor(Request $request)
    {
        $doctorValues = $request->toArray();

        foreach ($doctorValues as $doctorValue => $doctorElement) {
            $check = $this->checkValueCreateDoctor($doctorValue);

            if (!$check) {
                return false;
            }
        }

        return true;
    }

    protected function checkSpecialtyExists(int $id)
    {
        $specialty = $this->specialtyRepository->find($id);

        if (is_null($specialty)) {
            return false;
        }

        return true;
    }

    protected function checkStore(Request $request): bool|JsonResponse
    {
        $checkRequest = $this->checkRequestCreateDoctor($request);

        if (!$checkRequest) {
            $message = ['Error' => 'This Resource is Missing Parameters'];
            $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;

            return new JsonResponse($message, $statusCode);
        }

        $specialtyId = $this->getParameterInBody($request, 'Specialty');
        $checkSpecialty = $this->checkSpecialtyExists($specialtyId);

        if (!$checkSpecialty) {
            $message = ['Error' => 'Specialty Resorce Not Found'];
            $statusCode = Response::HTTP_NOT_FOUND;

            return new JsonResponse($message, $statusCode);
        }

        return true;
    }

    protected function findSpecialty(int $id): Specialty|false
    {
        $specialty = $this->specialtyRepository->find($id);

        if (is_null($specialty)) {
            return false;
        }

        return $specialty;
    }

    protected function getDoctorValues(Request $request)
    {
        $specialty = $this->findSpecialty($this->getParameterInBody($request, 'Specialty'));

        return [
            'Name' => $this->getParameterInBody($request, 'Name'),
            'Area' => $this->getParameterInBody($request, 'Area'),
            'Subscription' => $this->getParameterInBody($request, 'Subscription'),
            'Specialty' => $specialty,
        ];
    }

    protected function createEntityObject(Request $request): Doctor
    {
        $values = $this->getDoctorValues($request);

        $doctor = new Doctor();

        $doctor->setName($values['Name']);
        $doctor->setArea($values['Area']);
        $doctor->setSubscription($values['Subscription']);
        $doctor->setSpecialty($values['Specialty']);

        $this->specialtyRepository->add($values['Specialty'], true);

        return $doctor;
    }
}
