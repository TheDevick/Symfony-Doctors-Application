<?php

namespace App\Factory;

use App\Entity\Specialty;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Response;

class SpecialtyFactory implements Factory
{
    public array $specialtyRequiredElements = ['Title'];
    public array $specialtyAllElements = ['Title', 'Doctors', 'Description'];

    public function checkArrayToCreateSpecialty(array $array): object|bool
    {
        $collection = new ArrayCollection($array);

        foreach ($this->specialtyRequiredElements as $value) {
            $arrayContains = $collection->containsKey($value);

            if (!$arrayContains) {
                $message = ['Error' => 'This Resource is Missing Parameters'];
                $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;

                $error = (object) [
                    'message' => $message,
                    'statusCode' => $statusCode,
                ];

                return (object) [
                    'error' => $error,
                ];
            }
        }

        return true;
    }

    private function specialtySetValue(Specialty $specialty, string $element, $value)
    {
        $functionName = "set$element";

        $specialty->$functionName($value);
    }

    private function specialtySetValues(Specialty $specialty, array $values): Specialty
    {
        $collection = new ArrayCollection($values);

        foreach ($this->specialtyAllElements as $element) {
            $arrayContains = $collection->containsKey($element);

            if ($arrayContains) {
                $value = $collection->get($element);

                $this->specialtySetValue($specialty, $element, $value);
            }
        }

        return $specialty;
    }

    public function createEntity(array $data): Specialty|false
    {
        if (!$this->checkArrayToCreateSpecialty($data)) {
            return false;
        }

        $specialty = new Specialty();

        $this->specialtySetValues($specialty, $data);

        return $specialty;
    }

    public function updateSpecialty(Specialty $specialty, array $data): Specialty
    {
        $this->specialtySetValues($specialty, $data);

        return $specialty;
    }
}
