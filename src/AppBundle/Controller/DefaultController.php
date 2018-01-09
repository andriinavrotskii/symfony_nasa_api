<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends FOSRestController
{
    /**
     * @Route("/")
     *
     * @return JsonResponse
     */
    public function indexAction()
    {
        return new JsonResponse(['hello' => 'world!']);
    }
}
