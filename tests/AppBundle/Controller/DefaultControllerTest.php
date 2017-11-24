<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testDefault()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');
        $content = $client->getResponse()->getContent();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJsonStringEqualsJsonString($content, '{"data":{"hello":"world!"},"error":0,"error_message":""}');
    }

    public function testNotFound()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/not/exist/');
        $content = $client->getResponse()->getContent();

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        $this->assertJsonStringEqualsJsonString($content, '{"data":[],"error":1,"error_message":"Not found"}');
    }
}
