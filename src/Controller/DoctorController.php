<?php

namespace App\Controller;

use App\Factory\DoctorFactory;
use App\Repository\DoctorRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DoctorController extends BaseController
{
    public function __construct(
        private DoctorRepository $doctorRepository,
        private DoctorFactory $doctorFactory
    ) {
        parent::__construct($doctorRepository, $doctorFactory);
    }

    protected function jsonResponseNotFound(bool $mainEntity = true): JsonResponse
    {
        if ($mainEntity) {
            $error = ['Error' => 'Doctor Not Found'];
        } else {
            $error = ['Error' => 'Specialty Not Found'];
        }

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
}
