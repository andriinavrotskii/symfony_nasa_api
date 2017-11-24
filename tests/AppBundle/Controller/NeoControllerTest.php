<?php
/**
 * Created by PhpStorm.
 * User: navrotskiy
 * Date: 24.11.17
 * Time: 17:00
 */

namespace Tests\AppBundle\Controller;


use Tests\AppBundle\DataFixturesTestCase;

class NeoControllerTest extends DataFixturesTestCase
{

    public function testTest()
    {
        $data = $this->entityManager->findBy(['hazardous' => true]);
        dump($data);
    }
}