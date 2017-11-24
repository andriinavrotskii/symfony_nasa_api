<?php

namespace AppBundle\Listener;


use AppBundle\Exceptions\ApiErrorException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use JMS\Serializer\SerializerBuilder as JMSSerializer;


class ApiSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 0],
            KernelEvents::VIEW => ['onKernelView', 100],
        ];
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        $errorMessage = "Server Error";
        $code = 500;

        if ($exception instanceof ApiErrorException) {
            $errorMessage = $event->getException()->getMessage();
        } elseif ($exception instanceof NotFoundHttpException) {
            $errorMessage = "Not found";
            $code = 404;
        }

        $event->setResponse($this->getResponce([], $errorMessage, 1, $code));
    }


    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $data = $event->getControllerResult();

        $serializer = JMSSerializer::create()->build();
        $data = $serializer->toArray($data);

        $event->setResponse($this->getResponce($data));
    }


    public function getResponce(array $data, $errorMessage = '', $error = 0, $code = 200)
    {
        return new JsonResponse([
            'data' => $data,
            'error' => $error,
            'error_message' => $errorMessage,
        ], $code);
    }

}