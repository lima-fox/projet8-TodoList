<?php

namespace Tests\AppBundle\Controller;

use Blackfire\Client;
use Blackfire\Profile\Configuration;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    private $client;

    static $probe;

    static $blackfire;

    public static function setUpBeforeClass()
    {
        $config = new Configuration();
        $config->setTitle("Default");
        static::$blackfire = new Client();
        static::$probe = static::$blackfire->createProbe($config);
    }

    public static function tearDownAfterClass()
    {
        static::$blackfire->endProbe(static::$probe);
    }

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
