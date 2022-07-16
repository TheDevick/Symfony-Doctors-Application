<?php

namespace App\EventSubscriber;

use App\Exception\JsonNotFoundException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        if ($event->getThrowable() instanceof JsonNotFoundException) {
            $resource = $event->getThrowable()->getMessage();

            $response = new JsonResponse(['Error' => "$resource Not Found"], Response::HTTP_NOT_FOUND);

            $event->setResponse($response);
        }
    }
}
