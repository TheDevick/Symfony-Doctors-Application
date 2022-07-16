<?php

namespace App\Controller;

use App\Entity\Doctor;
use App\Entity\Entity;
use App\Exception\JsonNotFoundException;
use App\Factory\DoctorFactory;
use App\Repository\DoctorRepository;
use App\Repository\SpecialtyRepository;
use App\Request\Request as CustomRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DoctorController extends BaseController
{
    public function __construct(
        private SpecialtyRepository $specialtyRepository,
        private DoctorRepository $doctorRepository
    ) {
        parent::__construct($doctorRepository);
    }

    protected function jsonResponseNotFound(bool $mainEntity = true): JsonResponse
    {
        $entity = $mainEntity ? 'Doctor' : 'Specialty';

        $error = ['Error' => "$entity Not Found"];

        $statusCode = Response::HTTP_NOT_FOUND;

        return new JsonResponse($error, $statusCode);
    }

    protected function checkEntityOnRequest(CustomRequest $request): bool
    {
        $request->getBody();

        $elementsTocreate = Doctor::elementsToCreate()['required'];

        foreach ($elementsTocreate as $elementToCreate) {
            $check = $request->checkBodyKeyExists($elementToCreate, 'strtolower');
            if (!$check) {
                return false;
            }
        }

        return true;
    }

    private function setSpecialtyById(Doctor $doctor, int $specialtyId)
    {
        $specialty = $this->specialtyRepository->find($specialtyId);

        if (is_null($specialty)) {
            return false;
        }

        $doctor->setSpecialty($specialty);

        return $doctor;
    }

    private function setDoctorRequiredElements(Doctor $doctor, array $values): Doctor|false
    {
        $doctor->setName($values['Name']);
        $doctor->setArea($values['Area']);
        $doctor->setSubscription($values['Subscription']);

        $setSpecialty = $this->setSpecialtyById($doctor, $values['Specialty']);

        if (!$setSpecialty) {
            return false;
        }

        $this->specialtyRepository->flush();

        return $doctor;
    }

    private function setDoctorElements(Doctor $doctor, array $values): Doctor
    {
        $setRequired = $this->setDoctorRequiredElements($doctor, $values);

        if (!$setRequired) {
            throw new JsonNotFoundException('Specialty');
        }

        // Here, we don't have to set Values to unrequired elements
        // because Doctor Entity doesn't have unrequired elements

        return $doctor;
    }

    private function getDoctorElements(CustomRequest $request): array
    {
        $body = $request->getBody();

        $specialty = $this->specialtyRepository->find($body['Specialty']);

        if (is_null($specialty)) {
            throw new JsonNotFoundException('Specialty');
        }

        $elements = [
            'name' => $body['Name'],
            'subscription' => $body['Subscription'],
            'area' => $body['Area'],
            'specialty' => $specialty,
        ];

        return $elements;
    }

    protected function createEntityObject(CustomRequest $request): Doctor
    {
        $elements = $this->getDoctorElements($request);

        /** @var Doctor $doctor */
        $doctor = DoctorFactory::createOne($elements)->object();

        return $doctor;
    }

    public function updateEntityObject(Entity $entity, CustomRequest $request): Entity
    {
        $body = $request->getBody();

        $doctor = $this->setDoctorElements($entity, $body);

        return $doctor;
    }
}
