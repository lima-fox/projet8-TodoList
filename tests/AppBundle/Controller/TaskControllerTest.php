<?php


namespace Tests\AppBundle\Controller;


use AppBundle\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\UserCo;

class TaskControllerTest extends WebTestCase
{
    use UserCo;

    private $client;
    private $em;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->em = $this->client->getContainer()->get("doctrine.orm.entity_manager");
    }

    public function getLastTaskId()
    {
        return $this->em->createQueryBuilder()
            ->select('MAX(e.id)')
            ->from('AppBundle:Task', 'e')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function testTasksPageAsAnonymous()
    {
        $this->client->request('GET', '/tasks');

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    public function testTasksPageAsLogged()
    {
        $this->loginAsUser($this->client, $this);

        $this->client->request('GET', '/tasks');
        $this->assertEquals('200', $this->client->getResponse()->getStatusCode());
    }

    public function testCreateTaskPageAsLogged()
    {
        $this->loginAsUser($this->client, $this);

        $this->client->request('GET', '/tasks/create');
        $this->assertEquals('200', $this->client->getResponse()->getStatusCode());
    }

    public function testCreateTaskPageAsAnonymous()
    {
        $this->client->request('GET', '/tasks/create');
        $this->assertEquals('302', $this->client->getResponse()->getStatusCode());
    }

    public function testNominalFlow()
    {
        $this->CreateTask();
        $this->EditTask();
        $this->ToggleTask();
        $this->DeleteTask();
    }

    public function CreateTask()
    {
        $this->loginAsSuperAdmin($this->client, $this);

        $crawler = $this->client->request('GET', "/tasks/create");
        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => 'Un titre de test',
            'task[content]' => 'Un contenu de test'
        ]);
        $this->client->submit($form);

        $this->client->followRedirect();
        $this->assertEquals('200', $this->client->getResponse()->getStatusCode());
    }

    public function EditTask()
    {
        $this->loginAsSuperAdmin($this->client, $this);

        $id = $this->getLastTaskId();

        $crawler = $this->client->request('GET', "/tasks/$id/edit");
        $form = $crawler->selectButton('Modifier')->form([
            'task[title]' => 'Un titre de test modifié',
            'task[content]' => 'Un contenu de test modifié'
        ]);
        $this->client->submit($form);

        $this->client->followRedirect();
        $this->assertEquals('200', $this->client->getResponse()->getStatusCode());
    }

    public function ToggleTask()
    {
        $this->loginAsSuperAdmin($this->client, $this);
        $id = $this->getLastTaskId();
        $this->client->request('GET', "/tasks/$id/toggle");
        $this->client->followRedirect();
        $this->assertEquals('200', $this->client->getResponse()->getStatusCode());
    }

    public function DeleteTask()
    {
        $this->loginAsSuperAdmin($this->client, $this);
        $id = $this->getLastTaskId();
        $this->client->request('GET', "/tasks/$id/delete");
        $this->client->followRedirect();
        $this->assertEquals('200', $this->client->getResponse()->getStatusCode());
    }

    public function DeleteTaskAnonymous()
    {
        $task = new Task();
        $task->setUser(null);
        $task->setTitle('test title');
        $task->setContent('test content');

        $this->loginAsAdmin($this->client, $this);
        $id = $this->getLastTaskId();
        $this->client->request('GET', "/tasks/$id/delete");
        $this->client->followRedirect();
        $this->assertEquals('200', $this->client->getResponse()->getStatusCode());
        $this->assertContains('Seul un administrateur peut supprimer cette tâche', $this->client->getResponse()->getContent());

        $user = $this->em->getRepository("AppBundle:Task")->find($id);
        $this->em->remove($task);
        $this->em->flush();
    }

}