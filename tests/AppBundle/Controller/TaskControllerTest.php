<?php


namespace Tests\AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\UserCo;

class TaskControllerTest extends WebTestCase
{
    use UserCo;

    private $client;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    public function testTasksPageAsAnonymous()
    {
        $this->client->request('GET', '/tasks');

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    public function testTasksPageAsLogged()
    {
        $this->login($this->client, $this);

        $this->client->request('GET', '/tasks');
        $this->assertEquals('200', $this->client->getResponse()->getStatusCode());
    }

    public function testCreateTaskPageAsLogged()
    {
        $this->login($this->client, $this);

        $this->client->request('GET', '/tasks/create');
        $this->assertEquals('200', $this->client->getResponse()->getStatusCode());
    }

    public function testCreateTaskPageAsAnonymous()
    {
        $this->client->request('GET', '/tasks/create');
        $this->assertEquals('302', $this->client->getResponse()->getStatusCode());
    }

    public function testCreateTask()
    {
        $this->login($this->client, $this);

        $crawler = $this->client->request('GET', "/tasks/create");
        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => 'Un titre de test',
            'task[content]' => 'Un contenu de test'
        ]);
        $this->client->submit($form);

        $this->client->followRedirect();
        $this->assertEquals('200', $this->client->getResponse()->getStatusCode());
    }

    public function testEditTask()
    {
        $this->login($this->client, $this);

        $crawler = $this->client->request('GET', "/tasks/15/edit");
        $form = $crawler->selectButton('Modifier')->form([
            'task[title]' => 'Un titre de test modifié',
            'task[content]' => 'Un contenu de test modifié'
        ]);
        $this->client->submit($form);

        $this->client->followRedirect();
        $this->assertEquals('200', $this->client->getResponse()->getStatusCode());
    }
}