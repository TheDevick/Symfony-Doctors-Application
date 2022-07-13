<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class SpecialtyController extends AbstractController
{
    #[Route(path: '/specialties', name: 'specialties.index', methods: 'GET')]
    public function index(): JsonResponse
    {
        return new JsonResponse([
            "Path" => '/specialties',
            "Name" => 'Specialties.Index',
            "Methods" => 'GET'
        ]);
    }

    #[Route(path: '/specialties', name: 'specialties.store', methods: 'POST')]
    public function store(): JsonResponse
    {
        return new JsonResponse([
            "Path" => '/specialties',
            "Name" => 'Specialties.Store',
            "Methods" => 'POST'
        ]);
    }

    #[Route(path: '/specialties/{id}', name: 'specialties.show', methods: 'GET')]
    public function show(): JsonResponse
    {
        return new JsonResponse([
            "Path" => '/specialties/{id}',
            "Name" => 'Specialties.Show',
            "Methods" => 'GET'
        ]);
    }

    #[Route(path: '/specialties/{id}', name: 'specialties.update', methods: 'PUT')]
    public function update(): JsonResponse
    {
        return new JsonResponse([
            "Path" => '/specialties/{id}',
            "Name" => 'Specialties.Update',
            "Methods" => 'PUT'
        ]);
    }

    #[Route(path: '/specialties/{id}', name: 'specialties.destroy', methods: 'DELETE')]
    public function destroy(): JsonResponse
    {
        return new JsonResponse([
            "Path" => '/specialties/{id}',
            "Name" => 'Specialties.Destroy',
            "Methods" => 'DELETE'
        ]);
    }
}
