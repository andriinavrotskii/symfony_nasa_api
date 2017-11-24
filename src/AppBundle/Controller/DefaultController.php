<?php

namespace AppBundle\Controller;

use AppBundle\Exceptions\ApiErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;

class DefaultController extends Controller
{
    /**
     * @Rest\Get("/")
     */
    public function indexAction()
    {
        return ['hello' => 'world!'];
    }
}
