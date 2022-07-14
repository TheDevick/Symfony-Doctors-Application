<?php

namespace App\Controller;

use App\Factory\Factory;
use App\Repository\Repository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class BaseController extends AbstractController
{
    public function __construct(
        private Repository $repository,
        private Factory $factory
    ) {
    }
}
