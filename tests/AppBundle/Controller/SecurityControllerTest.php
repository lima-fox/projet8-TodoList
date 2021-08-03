<?php


namespace Tests\AppBundle\Controller;


use Blackfire\Client;
use Blackfire\Profile\Configuration;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\UserCo;

class SecurityControllerTest extends WebTestCase
{
    use UserCo;

    private $client;

    static $probe;

    static $blackfire;

    public static function setUpBeforeClass()
    {
        $config = new Configuration();
        $config->setTitle("Security");
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

    public function testLoginPage()
    {
        $this->client->request('GET', "/login");

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testLoginFormValidAuth()
    {
        $this->loginAsSuperAdmin($this->client, $this);
    }

    public function testBadCredentials()
    {
        $crawler = $this->client->request('GET', "/login");
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'limax',
            '_password' => 'errorpass'
        ]);
        $this->client->submit($form);

        $this->client->request("GET", "/");
        $this->client->followRedirect();
        $this->assertContains('Invalid credentials.', $this->client->getResponse()->getContent());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testLogout()
    {
        $this->loginAsSuperAdmin($this->client, $this);

        $this->client->request('GET', "/logout");

        $this->client->followRedirect();

        $this->client->request("GET", "/");
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

    }

}