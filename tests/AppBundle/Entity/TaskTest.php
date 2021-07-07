<?php


namespace Tests\AppBundle\Entity;


use AppBundle\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskTest extends WebTestCase
{
    private $task;
    private $client;
    private $validator;

    public function setUp()
    {
        $this->task = new Task();
        $this->task->setTitle('Un titre test');
        $this->task->setContent('Un contenu test');

        $this->client = static::createClient();
        $this->validator = $this->client->getContainer()->get('validator');
    }

    public function testValidEntity()
    {
        $errors = $this->validator->validate($this->task);

        $this->assertCount(0, $errors);
    }

    public function testTitleNotBlank()
    {
        $this->task->setTitle('');
        $errors = $this->validator->validate($this->task);

        $this->assertCount(1, $errors);
    }

    public function testContentNotBlank()
    {
        $this->task->setContent('');
        $errors = $this->validator->validate($this->task);

        $this->assertCount(1, $errors);
    }

}