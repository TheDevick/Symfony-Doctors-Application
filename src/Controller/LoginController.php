<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoginController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    private function emailOrPasswordIncorrect(): JsonResponse
    {
        $message = ['Error' => 'Email or Password Incorrect'];
        $statusCode = Response::HTTP_UNAUTHORIZED;

        return new JsonResponse($message, $statusCode);
    }
}
