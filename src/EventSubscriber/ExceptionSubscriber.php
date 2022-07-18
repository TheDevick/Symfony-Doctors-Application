<?php

namespace App\EventSubscriber;

use App\Exception\JsonNotFoundException;
use App\Exception\JsonUnprocessableEntityException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(private
        LoggerInterface $logger
        )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    private function onJsonNotFoundException(): JsonResponse
    {
        $message = ['Error' => 'Resource Not Found'];
        $statusCode = Response::HTTP_NOT_FOUND;

        $response = new JsonResponse($message, $statusCode);

        return $response;
    }

    private function onJsonUnprocessableEntityException(): JsonResponse
    {
        $message = ['Error' => 'This Resource is Missing Parameters'];
        $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;

        $response = new JsonResponse($message, $statusCode);

        return $response;
    }

    private function onGenericException(string $stackTrace): JsonResponse
    {
        $this->logger->critical('An Exception Occurred. {stack}', [
            'stack' => $stackTrace,
        ]);

        $message = ['Error' => 'Something went wrong! We\'re trying to fix it.'];
        $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;

        $response = new JsonResponse($message, $statusCode);

        return $response;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();

        if ($throwable instanceof JsonNotFoundException) {
            $response = $this->onJsonNotFoundException();
            $event->setResponse($response);
        }

        if ($throwable instanceof JsonUnprocessableEntityException) {
            $response = $this->onJsonUnprocessableEntityException();
            $event->setResponse($response);
        }

        $environment = getenv('APP_ENV');
        
        if ('prod' === $environment) {
            $stackTrace = $throwable->getTraceAsString();
            $response = $this->onGenericException($stackTrace);
            $event->setResponse($response);
        }
    }
}
