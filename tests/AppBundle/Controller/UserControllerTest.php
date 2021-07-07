<?php


namespace Tests\AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\UserCo;

class UserControllerTest extends WebTestCase
{
    use UserCo;

    private $client;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    public function testAdminUsersPageAsAnonymousOrUser()
    {
        $this->client->request('GET', '/admin/users');

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminUsersPageAsAdmin()
    {
        $this->loginAsAdmin($this->client, $this);
        $this->client->request('GET', '/admin/users');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateUserPage()
    {
        $this->client->request('GET', '/users/create');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateUserAdminPage()
    {
        $this->loginAsSuperAdmin($this->client, $this);

        $this->client->request('GET', '/owner/users/create');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateUser()
    {
        $crawler = $this->client->request('GET', '/users/create');
        $form = $crawler->selectButton('Ajouter')->form([
            'user[username]' => 'user_test1',
            'user[password][first]' => 'passtest',
            'user[password][second]' => 'passtest',
            'user[email]' => 'user_test@test.com'
        ]);
        $this->client->submit($form);

        $this->assertEquals('302', $this->client->getResponse()->getStatusCode());
    }
}