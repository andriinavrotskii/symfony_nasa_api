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
     * @Rest\View(statusCode=200)
     *
     * @return mixed
     * @throws ApiErrorException
     */
    public function hazardousAction()
    {
        try {
            /** @var NeoRepository $repository */
            $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Neo');
            $data = $repository->getHazardous();
            return $data;
        } catch (\LogicException $e) {
            throw new ApiErrorException('Error while searching for the most hazardous NEOs');
        }
    }

    /**
     * @Rest\Get("/fastest")
     * @Rest\View(statusCode=200)
     *
     * @param Request $request
     * @return mixed
     * @throws ApiErrorException
     */
    public function fastestAction(Request $request)
    {
        try {
            $hazardous = $request->get('hazardous') == 'true' ? true : false;

            /** @var NeoRepository $repository */
            $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Neo');
            $data = $repository->getFastestByHazardous($hazardous);
            return $data;
        } catch (\LogicException $e) {
            throw new ApiErrorException('Error while searching for the fastest NEO');
        }
    }
}