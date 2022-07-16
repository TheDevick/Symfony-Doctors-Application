<?php

namespace App\Factory;

use App\Entity\User;

class UserFactory extends Factory
{
    public function __construct()
    {
        parent::__construct();
    }

    protected static function getClass(): string
    {
        return User::class;
    }

    protected function getDefaults(): array
    {
        $defaults = [
            'email' => self::faker()->mail(),
            'roles' => [],
            'password' => self::faker()->password(),
        ];

        return $defaults;
    }
}
