<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

class DefaultController extends FOSRestController
{
    /**
     * @Rest\Get("/")
     * @Rest\View(statusCode=200)
     *
     * @return array
     */
    public function indexAction()
    {
        return ['hello' => 'world!'];
    }
}
