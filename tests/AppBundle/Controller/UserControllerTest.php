<?php


namespace Tests\AppBundle\Controller;


use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\UserCo;

class UserControllerTest extends WebTestCase
{
    use UserCo;

    private $client;
    private $em;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->em = $this->client->getContainer()->get("doctrine.orm.entity_manager");
    }

    public function getLastUserId()
    {
        return $this->em->createQueryBuilder()
            ->select('MAX(e.id)')
            ->from('AppBundle:User', 'e')
            ->getQuery()
            ->getSingleScalarResult();
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
        $id = $this->getLastUserId();
        $crawler = $this->client->request('GET', "/admin/users/$id/edit");
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
        $id = $this->getLastUserId();
        $crawler = $this->client->request('GET', "/owner/users/$id/edit");
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

    public function testDeleteUserAsSuperAdmin()
    {
        $this->loginAsSuperAdmin($this->client, $this);

        $id = $this->getLastUserId();
        $this->client->request('GET', "/admin/users/$id/delete");
        $this->client->followRedirect();
        $this->assertEquals('200', $this->client->getResponse()->getStatusCode());

        $id_delete = $this->em->getRepository("AppBundle:User")->find($id);
        $this->assertNull($id_delete);
    }

    public function testDeleteSuperAdminAsAdmin()
    {
        $user = new User();
        $user->setUsername(sprintf('usertestdelete_%d', rand(1,100)));
        $user->setPassword('pass');
        $user->setEmail(sprintf('user_test_delete%d@test.com', rand(1,100)));
        $user->setRoles(['ROLE_SUPER_ADMIN']);
        $this->em->persist($user);
        $this->em->flush();

        $this->loginAsAdmin($this->client, $this);
        $id = $user->getId();
        $this->client->request('GET', "/admin/users/$id/delete");
        $this->client->followRedirect();
        $this->assertEquals('200', $this->client->getResponse()->getStatusCode());
        $this->assertContains('Vous ne pouvez pas supprimer cet utilisateur.', $this->client->getResponse()->getContent());

        $user = $this->em->getRepository("AppBundle:User")->find($id);
        $this->em->remove($user);
        $this->em->flush();
    }
}