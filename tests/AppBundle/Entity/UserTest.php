<?php


namespace Tests\AppBundle\Entity;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Validator\ValidatorBuilder;

class UserTest extends WebTestCase
{
    private $user;

    private $client;

    public function setUp()
    {
        $this->user = new User();
        $this->user->setUsername('John');
        $this->user->setPassword('blob');
        $this->user->setEmail('john@example.com');
        $this->user->setRoles(['ROLE_USER']);

        $this->client = static::createClient();
    }

    public function testValidEntity()
    {
        $errors = $this->client->getContainer()->get('validator')->validate($this->user);

        $this->assertCount(0, $errors);
    }

    public function testUsernameNotBlank()
    {
        $this->user->setUsername('');
        $errors = $this->client->getContainer()->get('validator')->validate($this->user);

        $this->assertCount(1, $errors);
    }

    public function testEmailNotBlank()
    {
        $this->user->setEmail('');

        $errors = $this->client->getContainer()->get('validator')->validate($this->user);

        $this->assertCount(1, $errors);
    }

    public function testEmailType()
    {
        $this->user->setEmail('john');

        $errors = $this->client->getContainer()->get('validator')->validate($this->user);

        $this->assertCount(1, $errors);
    }

}