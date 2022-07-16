<?php

namespace App\Controller;

use App\Entity\Specialty;
use App\Factory\SpecialtyFactory;
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
        private SpecialtyFactory $specialtyFactory
    ) {
        parent::__construct($specialtyRepository, $specialtyFactory);
    }

    protected function jsonResponseNotFound(bool $mainEntity = true): JsonResponse
    {
        $entity = $mainEntity ? 'Specialty' : 'Doctor';

        $error = ['Error' => "$entity Not Found"];

        $statusCode = Response::HTTP_NOT_FOUND;

        return new JsonResponse($error, $statusCode);
    }

    protected function checkStore(CustomRequest $request): bool
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

    protected function createEntityObject(CustomRequest $request): Specialty
    {
        $specialty = new Specialty();

        $body = $request->getBody();

        $this->setSpecialtyRequiredElements($specialty, $body);

        $this->setSpecialtyUnrequiredElements($specialty, $body);

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
