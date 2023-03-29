<?php

namespace App\Test\Controller;

use App\Entity\Playlist;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PlaylistControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private PlaylistRepository $repository;
    private string $path = '/admin/playlist/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = (static::getContainer()->get('doctrine'))->getRepository(Playlist::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Playlist index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'playlist[name]' => 'Testing',
            'playlist[description]' => 'Testing',
        ]);

        self::assertResponseRedirects('/admin/playlist/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Playlist();
        $fixture->setName('My Title');
        $fixture->setDescription('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Playlist');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Playlist();
        $fixture->setName('My Title');
        $fixture->setDescription('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'playlist[name]' => 'Something New',
            'playlist[description]' => 'Something New',
        ]);

        self::assertResponseRedirects('/admin/playlist/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getName());
        self::assertSame('Something New', $fixture[0]->getDescription());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Playlist();
        $fixture->setName('My Title');
        $fixture->setDescription('My Title');

        $this->repository->add($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/admin/playlist/');
    }
}
