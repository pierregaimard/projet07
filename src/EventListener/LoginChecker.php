<?php

namespace App\EventListener;

use App\Service\Response\CustomErrorResponseManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

class LoginChecker implements EventSubscriberInterface
{
    public function __construct(
        private DecoderInterface $decoder,
        private CustomErrorResponseManager $errorResponseManager
    ){}

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [
                ['checkContentType', 3000]
            ]
        ];
    }

    public function checkContentType(RequestEvent $event)
    {
        if ($event->getRequest()->getPathInfo() !== '/login') {
            return;
        }

        $statusCode = 400;
        $type       = $this->errorResponseManager->getErrorType($event->getRequest()->headers->get('accept'));

        if ($type === false) {
            return;
        }

        # Missing Content-Type: application/json
        if ($event->getRequest()->headers->get('Content-Type') !== 'application/json') {
            $event->setResponse($this->errorResponseManager->getFormattedErrorResponse(
                'Content-Type must be set to \'application/json\'',
                $statusCode,
                $type
            ));
            return;
        }

        # Missing body authentication data
        if ($event->getRequest()->getContent(false) === "") {
            $event->setResponse($this->errorResponseManager->getFormattedErrorResponse(
                'Request body is empty. Did you miss to provide username and password ?',
                $statusCode,
                $type
            ));
            return;
        }

        $body = $this->decoder->decode($event->getRequest()->getContent(), 'json');

        # Credentials keys are missing in request body
        if (!array_key_exists('username', $body) || !array_key_exists('password', $body)) {
            $event->setResponse($this->errorResponseManager->getFormattedErrorResponse(
                'Invalid request body. You must provide \'username\' and \'password\' keys',
                $statusCode,
                $type
            ));
            return;
        }

        # Credentials informations are missing
        if ($body['username'] === "" || $body['password'] === "") {
            $event->setResponse($this->errorResponseManager->getFormattedErrorResponse(
                'Invalid request body. You must provide values for \'username\' and \'password\' keys',
                $statusCode,
                $type
            ));
            return;
        }
    }
}
