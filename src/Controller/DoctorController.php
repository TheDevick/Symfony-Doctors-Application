<?php

namespace App\Controller;

use App\Entity\Doctor;
use App\Repository\DoctorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DoctorController extends AbstractController
{
    public function __construct(
        private DoctorRepository $doctorRepository
    ) {
    }

    #[Route(path: '/doctors', name: 'doctors.index', methods: 'GET')]
    public function index(): JsonResponse
    {
        return new JsonResponse([
            'Path' => '/doctors',
            'Name' => 'Doctors.Index',
            'Methods' => 'GET',
        ]);
    }

    #[Route(path: '/doctors', name: 'doctors.store', methods: 'POST')]
    public function store(Request $request): JsonResponse
    {
        $data = $request->toArray();

        $doctor = new Doctor();
        $doctor->setName($data['Name']);
        $doctor->setArea($data['Area']);
        $doctor->setSubscription($data['Subscription']);

        $this->doctorRepository->add($doctor, true);

        return new JsonResponse($doctor);
    }

    #[Route(path: '/doctors/{id}', name: 'doctors.show', methods: 'GET')]
    public function show(): JsonResponse
    {
        return new JsonResponse([
            'Path' => '/doctors/{id}',
            'Name' => 'Doctors.Show',
            'Methods' => 'GET',
        ]);
    }

    #[Route(path: '/doctors/{id}', name: 'doctors.update', methods: 'PUT')]
    public function update(): JsonResponse
    {
        return new JsonResponse([
            'Path' => '/doctors/{id}',
            'Name' => 'Doctors.Update',
            'Methods' => 'PUT',
        ]);
    }

    #[Route(path: '/doctors/{id}', name: 'doctors.destroy', methods: 'DELETE')]
    public function destroy(): JsonResponse
    {
        return new JsonResponse([
            'Path' => '/doctors/{id}',
            'Name' => 'Doctors.Destroy',
            'Methods' => 'DELETE',
        ]);
    }
}
