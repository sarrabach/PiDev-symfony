<?php

namespace App\Test\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/guide/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(User::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('User index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'user[userFirstname]' => 'Testing',
            'user[userLastname]' => 'Testing',
            'user[userMail]' => 'Testing',
            'user[userPhone]' => 'Testing',
            'user[username]' => 'Testing',
            'user[password]' => 'Testing',
            'user[role]' => 'Testing',
            'user[lang1]' => 'Testing',
            'user[lang2]' => 'Testing',
            'user[lang3]' => 'Testing',
            'user[cityname]' => 'Testing',
            'user[nationality]' => 'Testing',
            'user[langue]' => 'Testing',
            'user[datebeg]' => 'Testing',
            'user[dateend]' => 'Testing',
            'user[disponibility]' => 'Testing',
            'user[idRelation]' => 'Testing',
        ]);

        self::assertResponseRedirects('/sweet/food/');

        self::assertSame(1, $this->getRepository()->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new User();
        $fixture->setUserFirstname('My Title');
        $fixture->setUserLastname('My Title');
        $fixture->setUserMail('My Title');
        $fixture->setUserPhone('My Title');
        $fixture->setUsername('My Title');
        $fixture->setPassword('My Title');
        $fixture->setRole('My Title');
        $fixture->setLang1('My Title');
        $fixture->setLang2('My Title');
        $fixture->setLang3('My Title');
        $fixture->setCityname('My Title');
        $fixture->setNationality('My Title');
        $fixture->setLangue('My Title');
        $fixture->setDatebeg('My Title');
        $fixture->setDateend('My Title');
        $fixture->setDisponibility('My Title');
        $fixture->setIdRelation('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('User');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new User();
        $fixture->setUserFirstname('Value');
        $fixture->setUserLastname('Value');
        $fixture->setUserMail('Value');
        $fixture->setUserPhone('Value');
        $fixture->setUsername('Value');
        $fixture->setPassword('Value');
        $fixture->setRole('Value');
        $fixture->setLang1('Value');
        $fixture->setLang2('Value');
        $fixture->setLang3('Value');
        $fixture->setCityname('Value');
        $fixture->setNationality('Value');
        $fixture->setLangue('Value');
        $fixture->setDatebeg('Value');
        $fixture->setDateend('Value');
        $fixture->setDisponibility('Value');
        $fixture->setIdRelation('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'user[userFirstname]' => 'Something New',
            'user[userLastname]' => 'Something New',
            'user[userMail]' => 'Something New',
            'user[userPhone]' => 'Something New',
            'user[username]' => 'Something New',
            'user[password]' => 'Something New',
            'user[role]' => 'Something New',
            'user[lang1]' => 'Something New',
            'user[lang2]' => 'Something New',
            'user[lang3]' => 'Something New',
            'user[cityname]' => 'Something New',
            'user[nationality]' => 'Something New',
            'user[langue]' => 'Something New',
            'user[datebeg]' => 'Something New',
            'user[dateend]' => 'Something New',
            'user[disponibility]' => 'Something New',
            'user[idRelation]' => 'Something New',
        ]);

        self::assertResponseRedirects('/guide/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getUserFirstname());
        self::assertSame('Something New', $fixture[0]->getUserLastname());
        self::assertSame('Something New', $fixture[0]->getUserMail());
        self::assertSame('Something New', $fixture[0]->getUserPhone());
        self::assertSame('Something New', $fixture[0]->getUsername());
        self::assertSame('Something New', $fixture[0]->getPassword());
        self::assertSame('Something New', $fixture[0]->getRole());
        self::assertSame('Something New', $fixture[0]->getLang1());
        self::assertSame('Something New', $fixture[0]->getLang2());
        self::assertSame('Something New', $fixture[0]->getLang3());
        self::assertSame('Something New', $fixture[0]->getCityname());
        self::assertSame('Something New', $fixture[0]->getNationality());
        self::assertSame('Something New', $fixture[0]->getLangue());
        self::assertSame('Something New', $fixture[0]->getDatebeg());
        self::assertSame('Something New', $fixture[0]->getDateend());
        self::assertSame('Something New', $fixture[0]->getDisponibility());
        self::assertSame('Something New', $fixture[0]->getIdRelation());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new User();
        $fixture->setUserFirstname('Value');
        $fixture->setUserLastname('Value');
        $fixture->setUserMail('Value');
        $fixture->setUserPhone('Value');
        $fixture->setUsername('Value');
        $fixture->setPassword('Value');
        $fixture->setRole('Value');
        $fixture->setLang1('Value');
        $fixture->setLang2('Value');
        $fixture->setLang3('Value');
        $fixture->setCityname('Value');
        $fixture->setNationality('Value');
        $fixture->setLangue('Value');
        $fixture->setDatebeg('Value');
        $fixture->setDateend('Value');
        $fixture->setDisponibility('Value');
        $fixture->setIdRelation('Value');

        $$this->manager->remove($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/guide/');
        self::assertSame(0, $this->repository->count([]));
    }
}
