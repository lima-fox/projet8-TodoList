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
            'user[username]' => sprintf('usernametest_%d', rand(1,1000)),
            'user[password][first]' => 'passtest',
            'user[password][second]' => 'passtest',
            'user[email]' => sprintf('user_test_%d@test.com', rand(1,1000))
        ]);
        $this->client->submit($form);

        $this->assertEquals('302', $this->client->getResponse()->getStatusCode());
    }

    public function testCreateUserAsSuperAdmin()
    {
        $this->loginAsSuperAdmin($this->client, $this);

        $crawler = $this->client->request('GET', '/owner/users/create');
        $form = $crawler->selectButton('Ajouter')->form([
            'admin_user[username]' => sprintf('usernametest_%d', rand(1,1000)),
            'admin_user[password][first]' => 'passtest',
            'admin_user[password][second]' => 'passtest',
            'admin_user[email]' => sprintf('user_test_%d@test.com', rand(1,1000)) ,
            'admin_user[roles]' => 0
        ]);
        $this->client->submit($form);

        $this->assertEquals('302', $this->client->getResponse()->getStatusCode());
    }

    public function testEditUserAsAdmin()
    {
        $this->loginAsAdmin($this->client, $this);

        $crawler = $this->client->request('GET', '/admin/users/4/edit');
        $form = $crawler->selectButton('Modifier')->form([
            'user[username]' => sprintf('usernametest_%d', rand(1,1000)),
            'user[password][first]' => 'passtest',
            'user[password][second]' => 'passtest',
            'user[email]' => sprintf('user_test_%d@test.com', rand(1,1000))
        ]);
        $this->client->submit($form);

        $this->assertEquals('302', $this->client->getResponse()->getStatusCode());
    }

    public function testEditUserAsSuperAdmin()
    {
        $this->loginAsSuperAdmin($this->client, $this);

        $crawler = $this->client->request('GET', '/owner/users/5/edit');
        $form = $crawler->selectButton('Modifier')->form([
            'admin_user[username]' => sprintf('usernametest_%d', rand(1,1000)),
            'admin_user[password][first]' => 'passtest',
            'admin_user[password][second]' => 'passtest',
            'admin_user[email]' => sprintf('user_test_%d@test.com', rand(1,1000)) ,
            'admin_user[roles]' => 0
        ]);
        $this->client->submit($form);

        $this->assertEquals('302', $this->client->getResponse()->getStatusCode());
    }
}