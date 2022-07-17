<?php

namespace App\EventSubscriber;

use App\Exception\JsonNotFoundException;
use App\Exception\JsonUnprocessableEntityException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Throwable;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    private function onJsonNotFoundException(Throwable $throwable): JsonResponse
    {
        $resource = $throwable->getMessage();

        $response = new JsonResponse(['Error' => "$resource Not Found"], Response::HTTP_NOT_FOUND);

        return $response;
    }

    private function onJsonUnprocessableEntityException(): JsonResponse
    {
        $message = ['Error' => 'This Resource is Missing Parameters'];
        $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;

        return new JsonResponse($message, $statusCode);
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();

        if ($throwable instanceof JsonNotFoundException) {
            $response = $this->onJsonNotFoundException($throwable);
            $event->setResponse($response);
        }

        if ($throwable instanceof JsonUnprocessableEntityException) {
            $response = $this->onJsonUnprocessableEntityException();
            $event->setResponse($response);
        }
    }
}
