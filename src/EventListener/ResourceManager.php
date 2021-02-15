<?php

namespace App\EventListener;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Exception\ResourceNotFoundException;
use App\Service\Response\CustomErrorResponseManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ResourceManager implements EventSubscriberInterface
{
    public function __construct(
        private DecoderInterface $decoder,
        private CustomErrorResponseManager $errorResponseManager
    ){}

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => ['checkCollectionNullResult'],
        ];
    }

    public function checkCollectionNullResult(ResponseEvent $event)
    {
        if (!$event->getResponse()->isOk() || $event->getRequest()->getPathInfo() === '/docs') {
            return null;
        }

        # For symfony debug tool bar
        if ($event->getRequest()->isXmlHttpRequest()) {
            return null;
        }

        $data        = $this->decoder->decode($event->getResponse()->getContent(), 'json');
        $contentType = $event->getResponse()->headers->get('Content-Type');

        $json    = str_contains($contentType, 'application/json');
        $jsonld  = str_contains($contentType, 'application/ld+json');
        $jsonhal = str_contains($contentType, 'application/hal+json');

        $message    = 'Not found';
        $statusCode = 404;

        if ($json && is_array($data) && empty($data)) {
            $event->setResponse($this->errorResponseManager->getFormattedErrorResponse(
                $message,
                $statusCode,
                CustomErrorResponseManager::TYPE_JSON
            ));
        }
        if ($jsonld && array_key_exists('hydra:member', $data) && empty($data['hydra:member'])) {
            $event->setResponse($this->errorResponseManager->getFormattedErrorResponse(
                $message,
                $statusCode,
                CustomErrorResponseManager::TYPE_JSONLD
            ));
        }
        if ($jsonhal && !array_key_exists('_embedded', $data) && array_key_exists('totalItems', $data)) {
            $event->setResponse($this->errorResponseManager->getFormattedErrorResponse(
                $message,
                $statusCode,
                CustomErrorResponseManager::TYPE_JSONHAL
            ));
        }
    }
}
