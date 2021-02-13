<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

class LoginChecker implements EventSubscriberInterface
{
    public function __construct(
        private DecoderInterface $decoder
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

        # Missing Content-Type: application/json
        if ($event->getRequest()->headers->get('Content-Type') !== 'application/json') {
            $event->setResponse($this->getFormattedResponse('Content-Type must be set to \'application/json\''));
            return;
        }

        # Missing body authentication data
        if ($event->getRequest()->getContent(false) === "") {
            $event->setResponse($this->getFormattedResponse(
                'Request body is empty. Did you miss to provide username and password ?'
            ));
            return;
        }

        $body = $this->decoder->decode($event->getRequest()->getContent(), 'json');

        # Credentials keys are missing in request body
        if (!array_key_exists('username', $body) || !array_key_exists('password', $body)) {
            $event->setResponse($this->getFormattedResponse(
                'Invalid request body. You must provide \'username\' and \'password\' keys'
            ));
            return;
        }

        # Credentials informations are missing
        if ($body['username'] === "" || $body['password'] === "") {
            $event->setResponse($this->getFormattedResponse(
                'Invalid request body. You must provide values for \'username\' and \'password\' keys'
            ));
            return;
        }
    }

    /**
     * @param string $message
     *
     * @return JsonResponse
     */
    private function getFormattedResponse(string $message): JsonResponse
    {
        return new JsonResponse(
            [
                'type' => 'https://tools.ietf.org/html/rfc2616#section-10',
                'title' => 'An error occurred',
                'status' => '400',
                'detail' => $message
            ],
            400
        );
    }
}
