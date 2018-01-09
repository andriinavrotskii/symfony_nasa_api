<?php

namespace AppBundle\Listener;


use AppBundle\Exceptions\ApiErrorException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;


class ApiSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 0],
            KernelEvents::VIEW => ['onKernelView', 100],
        ];
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
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
        $event->setResponse($this->getJsonResponse(
            $this->getResponceData([], $errorMessage, 1),
            $code
        ));
    }

    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $data = $event->getControllerResult();
        $event->setControllerResult($this->getResponceData($data));
    }

    /**
     * @param array $data
     * @param string $errorMessage
     * @param int $error
     * @return array
     */
    private function getResponceData(array $data, $errorMessage = '', $error = 0)
    {
        if (isset($data['data'])) {
            return $data;
        }

        return [
            'data' => $data,
            'error' => $error,
            'error_message' => $errorMessage,
        ];
    }

    /**
     * @param array $data
     * @param $code
     * @return JsonResponse
     */
    private function getJsonResponse(array $data, $code)
    {
        if ($code) {
            return new JsonResponse($data, $code);
        }

        return new JsonResponse($data);
    }

}