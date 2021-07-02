<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    private $client;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    /** @test  */
    public function testPageIsFound() {
        $this->client->request('GET', "/");

        $this->assertTrue($this->client->getResponse()->isRedirection());
    }
}
