<?php


namespace Tests;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Client;


Trait UserCo
{
    private function login (Client $client, WebTestCase $case, $user, $pass)
    {
        $crawler = $client->request('GET', "/login");
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => $user,
            '_password' => $pass
        ]);
        $client->submit($form);

        $client->request("GET", "/");
        $case->assertContains('Bienvenue sur Todo List' , $client->getResponse()->getContent());
        $case->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function loginAsSuperAdmin(Client $client, WebTestCase $case)
    {
        $this->login($client, $case, "limax", "blob");
    }

    public function loginAsAdmin(Client $client, WebTestCase $case)
    {
        $this->login($client, $case, 'lima', 'blob');
    }

    public function loginAsUser(Client $client, WebTestCase $case)
    {
        $this->login($client, $case, 'nyamou', 'miaou');
    }
}