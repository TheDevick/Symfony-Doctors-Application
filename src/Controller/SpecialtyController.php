<?php

namespace App\Controller;

use App\Entity\Entity;
use App\Entity\Specialty;
use App\Repository\DoctorRepository;
use App\Repository\SpecialtyRepository;
use App\Request\Request as CustomRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SpecialtyController extends BaseController
{
    public function __construct(
        private DoctorRepository $doctorRepository,
        private SpecialtyRepository $specialtyRepository,
    ) {
        parent::__construct($specialtyRepository);
    }

    protected function jsonResponseNotFound(bool $mainEntity = true): JsonResponse
    {
        $entity = $mainEntity ? 'Specialty' : 'Doctor';

        $error = ['Error' => "$entity Not Found"];

        $statusCode = Response::HTTP_NOT_FOUND;

        return new JsonResponse($error, $statusCode);
    }

    protected function checkEntityOnRequest(CustomRequest $request): bool
    {
        $elementsTocreate = Specialty::elementsToCreate()['required'];
        $body = $request->getBody();

        foreach ($elementsTocreate as $elementToCreate) {
            $check = $request->checkBodyKeyExists($elementToCreate, 'strtolower');
            if (!$check) {
                return false;
            }
        }

        return true;
    }

    private function setSpecialtyRequiredElements(Specialty $specialty, array $values): Specialty
    {
        $specialty->setTitle($values['Title']);

        return $specialty;
    }

    private function setSpecialtyUnrequiredElements(Specialty $specialty, array $values): Specialty
    {
        if (array_key_exists('Description', $values)) {
            $description = $values['Description'];
            $specialty->setDescription($description);
        }

        return $specialty;
    }

    private function setSpecialtyElements(Specialty $specialty, array $values): Specialty
    {
        $this->setSpecialtyRequiredElements($specialty, $values);

        $this->setSpecialtyUnrequiredElements($specialty, $values);

        return $specialty;
    }

    protected function createEntityObject(CustomRequest $request): Specialty
    {
        $entity = new Specialty();

        $body = $request->getBody();

        $specialty = $this->setSpecialtyElements($entity, $body);

        return $specialty;
    }

    public function updateEntityObject(Entity $entity, CustomRequest $request): Entity
    {
        $body = $request->getBody();

        $specialty = $this->setSpecialtyElements($entity, $body);

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
