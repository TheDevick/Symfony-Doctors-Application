<?php

namespace App\Factory;

use App\Entity\Doctor;
use Doctrine\Common\Collections\ArrayCollection;

class DoctorFactory
{
    public array $doctorRequiredElements = ['Name', 'Area', 'Subscription'];
    public array $doctorAllElements = ['Name', 'Area', 'Subscription'];

    public function checkArrayToCreateDoctor(array $array): bool
    {
        $collection = new ArrayCollection($array);

        foreach ($this->doctorRequiredElements as $value) {
            $arrayContains = $collection->containsKey($value);

            if (!$arrayContains) {
                return false;
            }
        }

        return true;
    }

    private function doctorSetValue(Doctor $doctor, string $element, $value)
    {
        $functionName = "set$element";

        $doctor->$functionName($value);
    }

    private function doctorSetValues(Doctor $doctor, array $values): Doctor
    {
        $collection = new ArrayCollection($values);

        foreach ($this->doctorAllElements as $element) {
            $arrayContains = $collection->containsKey($element);

            if ($arrayContains) {
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
