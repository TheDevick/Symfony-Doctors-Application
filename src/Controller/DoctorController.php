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
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class DoctorController extends BaseController
{
    public function __construct(
        private SpecialtyRepository $specialtyRepository,
        private DoctorRepository $doctorRepository,
        private CacheItemPoolInterface $cacheItemPool,
        private LoggerInterface $logger
    ) {
        parent::__construct($doctorRepository, $cacheItemPool, $logger);
    }

    protected function checkEntityOnRequest(bool $throwException = true): bool
    {
        $request = CustomRequest::createRequest();

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

    private function getDoctorElements(): array
    {
        $request = CustomRequest::createRequest();

        $body = $request->getBody();

        $specialty = $this->specialtyRepository->find($body['Specialty']);

        if (is_null($specialty)) {
            throw new JsonNotFoundException();
        }

        $elements = [
            'name' => $body['Name'],
            'subscription' => $body['Subscription'],
            'area' => $body['Area'],
            'specialty' => $specialty,
        ];

        return $elements;
    }

    protected function createEntityObject(): Doctor
    {
        $elements = $this->getDoctorElements();

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

    protected function cachePrefix(): string
    {
        return 'doctor';
    }
}
