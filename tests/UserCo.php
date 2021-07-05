<?php


namespace Tests;


use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;


Trait UserCo
{
    public function login (Client $client, WebTestCase $case)
    {
        $crawler = $client->request('GET', "/login");
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'limax',
            '_password' => 'blob'
        ]);
        $client->submit($form);

        $client->request("GET", "/");
        $case->assertContains('Bienvenue sur Todo List' , $client->getResponse()->getContent());
        $case->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}