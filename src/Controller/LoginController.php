<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Request\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

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

    #[Route(path: '/login', name: 'users.login', methods: ['POST'])]
    public function login()
    {
        $request = Request::createRequest();

        $email = $request->getParameterBody('Email');
        $password = $request->getParameterBody('Password');

        if (is_null($email) || is_null($password)) {
            $message = ['Error' => 'This Resource is Missing Parameters'];
            $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;

            return new JsonResponse($message, $statusCode);
        }

        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (is_null($user)) {
            return $this->emailOrPasswordIncorrect();
        }

        $passwordIsValid = $this->passwordHasher->isPasswordValid($user, $password);

        if (!$passwordIsValid) {
            return $this->emailOrPasswordIncorrect();
        }

        return new JsonResponse(['Success' => 'Login is Correct']);
    }
}
