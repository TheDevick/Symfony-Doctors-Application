<?php

namespace App\Controller;

use App\Entity\Doctor;
use App\Entity\Entity;
use App\Exception\JsonNotFoundException;
use App\Exception\JsonUnprocessableEntityException;
use App\Factory\DoctorFactory;
use App\Repository\DoctorRepository;
use App\Repository\SpecialtyRepository;
use App\Request\Request as CustomRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccess;

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

    protected function checkEntityOnRequest(CustomRequest $request, bool $throwException = true): bool
    {
        $elementsTocreate = Doctor::elementsToCreate()['required'];

        foreach ($elementsTocreate as $elementToCreate) {
            $check = $request->checkBodyKeyExists($elementToCreate, 'strtolower');
            if (!$check) {
                if ($throwException) {
                    throw new JsonUnprocessableEntityException();
                }

                return false;
            }
        }

        return true;
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

    private function setDoctorValues(Doctor $doctor, array $values)
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        foreach ($values as $key => $value) {
            $propertyAccessor->setValue($doctor, $key, $value);
        }
    }

    private function updateDoctorValues(Doctor $currentDoctor, Doctor $newDoctor)
    {
        $newValues = [
            'name' => $newDoctor->getName(),
            'area' => $newDoctor->getArea(),
            'subscription' => $newDoctor->getSubscription(),
            'specialty' => $newDoctor->getSpecialty(),
        ];

        $this->setDoctorValues($currentDoctor, $newValues);

        $this->doctorRepository->flush();
    }

    public function updateEntityObject(Entity $entity, CustomRequest $request): Entity
    {
        $elements = $this->getDoctorElements($request);

        /** @var Doctor $newEntity */
        $newEntity = DoctorFactory::new()->withoutPersisting()->createOne($elements)->object();

        $this->updateDoctorValues($entity, $newEntity);

        return $entity;
    }
}
