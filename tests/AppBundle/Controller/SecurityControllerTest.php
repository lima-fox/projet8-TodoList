<?php


namespace Tests\AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
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
        $crawler = $this->client->request('GET', "/login");
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'limax',
            '_password' => 'blob'
        ]);
        $this->client->submit($form);

        $this->client->request("GET", "/");
        $this->assertContains('Bienvenue sur Todo List' , $this->client->getResponse()->getContent());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
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
        $crawler = $this->client->request('GET', "/login");
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'limax',
            '_password' => 'blob'
        ]);
        $this->client->submit($form);

        $this->client->request("GET", "/");
        $this->assertContains('Bienvenue sur Todo List' , $this->client->getResponse()->getContent());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->client->request('GET', "/logout");

        $this->client->followRedirect();

        $this->client->request("GET", "/");
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

    }

}