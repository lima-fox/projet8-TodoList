<?php


namespace Tests\AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\UserCo;

class SecurityControllerTest extends WebTestCase
{
    use UserCo;

    private $client;

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
        $this->login($this->client, $this);
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
        $this->login($this->client, $this);

        $this->client->request('GET', "/logout");

        $this->client->followRedirect();

        $this->client->request("GET", "/");
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

    }

}