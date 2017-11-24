<?php
/**
 * Created by PhpStorm.
 * User: navrotskiy
 * Date: 21.11.17
 * Time: 17:07
 */

namespace AppBundle\Controller;

use AppBundle\Repository\NeoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use Doctrine\ORM\ORMException;
use AppBundle\Exceptions\ApiErrorException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class NeoController
 * @package AppBundle\Controller
 * @Route("/neo")
 */
class NeoController extends Controller
{
    /**
     * @Rest\Get("/hazardous")
     */
    public function hazardousAction()
    {
        try {
            $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Neo');
            $data = $repository->getHazardous();
            return $data;
        } catch (ORMException $e) {
            throw new ApiErrorException('Error while searching for the most hazardous NEOs');
        }
    }

    /**
     * @Rest\Get("/fastest")
     */
    public function fastestAction(Request $request)
    {
        try {
            $hazardous = $request->get('hazardous') == 'true' ? true : false;

            $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Neo');
            $data = $repository->getFastestByHazardous($hazardous);
            return $data;
        } catch (ORMException $e) {
            throw new ApiErrorException('Error while searching for the fastest NEO');
        }
    }
}