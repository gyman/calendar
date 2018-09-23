<?php

namespace App\Listener;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionToJson
{
    /** @var bool */
    private $debug = false;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(bool $debug, LoggerInterface $logger)
    {
        $this->debug = $debug;
        $this->logger = $logger;
    }

    public function onKernelException(GetResponseForExceptionEvent $event): void
    {
        if (!strstr($event->getRequest()->getUri(), '/calendar')) {
            return;
        }

        $exception = $event->getException();
        $status = Response::HTTP_INTERNAL_SERVER_ERROR;

        if ($exception instanceof NotFoundHttpException) {
            $status = Response::HTTP_NOT_FOUND;
        }

        $this->logException(
            $exception,
            sprintf(
                'Uncaught PHP Exception %s: "%s" at %s line %s',
                get_class($exception),
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine()
            )
        );

        $event->setResponse(new JsonResponse([
            'data' => [],
            'errors' => [
                'exception' => $exception->getMessage(),
            ],
        ], $status));

        $event->stopPropagation();
    }

    protected function logException(\Exception $exception, $message): void
    {
        if (null !== $this->logger) {
            if (!$exception instanceof HttpExceptionInterface || $exception->getStatusCode() >= 500) {
                $this->logger->critical($message, ['exception' => $exception]);
            } else {
                $this->logger->error($message, ['exception' => $exception]);
            }
        }
    }
}
