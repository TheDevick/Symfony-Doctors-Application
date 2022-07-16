<?php

namespace App\Factory;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFactory extends Factory
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected static function getClass(): string
    {
        return User::class;
    }

    protected function getDefaults(): array
    {
        $userName = self::faker()->userName();
        $roles = [];
        $plainPassword = self::faker()->password();

        $defaults = [
            'userName' => $userName,
            'roles' => $roles,
            'password' => $plainPassword,
        ];

        return $defaults;
    }

    protected function initialize(): self
    {
        return $this->afterInstantiate(function (User $user) {
            $plainPassword = $user->getPassword();
            $passwordHash = $this->passwordHasher->hashPassword($user, $plainPassword);

            $user->setPassword($passwordHash);
        });
    }
}
