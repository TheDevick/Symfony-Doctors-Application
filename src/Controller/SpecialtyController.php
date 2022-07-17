<?php

namespace App\Controller;

use App\Entity\Entity;
use App\Entity\Specialty;
use App\Exception\JsonNotFoundException;
use App\Exception\JsonUnprocessableEntityException;
use App\Factory\SpecialtyFactory;
use App\Repository\DoctorRepository;
use App\Repository\SpecialtyRepository;
use App\Request\Request as CustomRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\Annotation\Route;

class SpecialtyController extends BaseController
{
    public function __construct(
        private DoctorRepository $doctorRepository,
        private SpecialtyRepository $specialtyRepository,
    ) {
        parent::__construct($specialtyRepository);
    }

    protected function checkEntityOnRequest(CustomRequest $request, bool $throwException = true): bool
    {
        $elementsTocreate = Specialty::elementsToCreate()['required'];

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

    private function getSpecialtyElements(CustomRequest $request): array
    {
        $body = $request->getBody();

        $elements = [
            'title' => $body['Title'],
            'description' => $body['Description'] ?? null,
        ];

        return $elements;
    }

    protected function createEntityObject(CustomRequest $request): Specialty
    {
        $elements = $this->getSpecialtyElements($request);

        /** @var Specialty $specialty */
        $specialty = SpecialtyFactory::createOne($elements)->object();

        return $specialty;
    }

    private function setSpecialtyValues(Specialty $specialty, array $values)
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        foreach ($values as $key => $value) {
            $propertyAccessor->setValue($specialty, $key, $value);
        }
    }

    private function updateSpecialtyValues(Specialty $currentSpecialty, Specialty $newSpecialty)
    {
        $newValues = [
            'title' => $newSpecialty->getTitle(),
            'description' => $newSpecialty->getDescription(),
        ];

        $this->setSpecialtyValues($currentSpecialty, $newValues);

        $this->specialtyRepository->flush();
    }

    public function updateEntityObject(Entity $entity, CustomRequest $request): Entity
    {
        $elements = $this->getSpecialtyElements($request);

        /** @var Specialty $newEntity */
        $newEntity = SpecialtyFactory::new()->withoutPersisting()->createOne($elements)->object();

        $this->updateSpecialtyValues($entity, $newEntity);

        return $entity;
    }

    #[Route(path: '/api/specialties/{id}/doctors', name: 'specialties.showDoctors', methods: 'GET')]
    public function showDoctors(int $id)
    {
        $doctors = $this->doctorRepository->findBy(['specialty' => $id]);

        if (empty($doctors)) {
            throw new JsonNotFoundException();
        }

        foreach ($doctors as $doctor) {
            $view[] = $doctor->view();
        }

        return new JsonResponse($view);
    }
}
