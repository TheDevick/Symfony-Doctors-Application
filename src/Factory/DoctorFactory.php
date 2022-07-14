<?php

namespace App\Factory;

use App\Entity\Doctor;
use App\Repository\SpecialtyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Response;

class DoctorFactory
{
    public function __construct(private SpecialtyRepository $specialtyRepository)
    {
    }

    public array $doctorRequiredElements = ['Name', 'Area', 'Subscription', 'SpecialtyId'];
    public array $doctorAllElements = ['Name', 'Area', 'Subscription', 'SpecialtyId'];

    public function checkArrayToCreateDoctor(array $array): array|bool
    {
        $collection = new ArrayCollection($array);

        foreach ($this->doctorRequiredElements as $value) {
            $arrayContains = $collection->containsKey($value);

            if (!$arrayContains) {
                return [
                    'Error' => 'This Resource is Missing Parameters',
                    'Status Code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                ];
            }
        }

        if (!$this->checkSpecialtyExists($array)) {
            return [
                'Error' => 'Specialty Resorce Not Found',
                'Status Code' => Response::HTTP_NOT_FOUND,
            ];
        }

        return true;
    }

    private function checkSpecialtyExists(array $data): bool
    {
        $specialtyId = $data['SpecialtyId'];

        $specialty = $this->specialtyRepository->find($specialtyId);

        if (is_null($specialty)) {
            return false;
        }

        return true;
    }

    private function doctorSetValue(Doctor $doctor, string $element, $value)
    {
        $functionName = "set$element";

        $doctor->$functionName($value);
    }

    private function setSpecialty(Doctor $doctor, int $id)
    {
        $specialty = $this->specialtyRepository->find($id);

        $doctor->setSpecialty($specialty);

        $this->specialtyRepository->add($specialty, true);
    }

    private function doctorSetValues(Doctor $doctor, array $values): Doctor
    {
        $collection = new ArrayCollection($values);

        foreach ($this->doctorAllElements as $element) {
            $arrayContains = $collection->containsKey($element);

            if ($arrayContains) {
                if ('SpecialtyId' == $element) {
                    $specialtyId = $collection->get($element);

                    $this->setSpecialty($doctor, $specialtyId);

                    continue;
                }

                $value = $collection->get($element);

                $this->doctorSetValue($doctor, $element, $value);
            }
        }

        return $doctor;
    }

    public function createDoctor(array $data): Doctor|false
    {
        if (!$this->checkArrayToCreateDoctor($data)) {
            return false;
        }

        if (!$this->checkSpecialtyExists($data)) {
            return false;
        }

        $doctor = new Doctor();

        $this->doctorSetValues($doctor, $data);

        return $doctor;
    }

    public function updateDoctor(Doctor $doctor, array $data): Doctor
    {
        $this->doctorSetValues($doctor, $data);

        return $doctor;
    }
}
