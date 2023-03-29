<?php

namespace App\Test\Controller;

use App\Entity\Formation;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FormationControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private FormationRepository $repository;
    private string $path = '/admin/formation/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = (static::getContainer()->get('doctrine'))->getRepository(Formation::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Formation index');

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
            'formation[publishedAt]' => 'Testing',
            'formation[title]' => 'Testing',
            'formation[description]' => 'Testing',
            'formation[videoId]' => 'Testing',
            'formation[playlist]' => 'Testing',
            'formation[categories]' => 'Testing',
        ]);

        self::assertResponseRedirects('/admin/formation/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Formation();
        $fixture->setPublishedAt('My Title');
        $fixture->setTitle('My Title');
        $fixture->setDescription('My Title');
        $fixture->setVideoId('My Title');
        $fixture->setPlaylist('My Title');
        $fixture->setCategories('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Formation');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Formation();
        $fixture->setPublishedAt('My Title');
        $fixture->setTitle('My Title');
        $fixture->setDescription('My Title');
        $fixture->setVideoId('My Title');
        $fixture->setPlaylist('My Title');
        $fixture->setCategories('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'formation[publishedAt]' => 'Something New',
            'formation[title]' => 'Something New',
            'formation[description]' => 'Something New',
            'formation[videoId]' => 'Something New',
            'formation[playlist]' => 'Something New',
            'formation[categories]' => 'Something New',
        ]);

        self::assertResponseRedirects('/admin/formation/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getPublishedAt());
        self::assertSame('Something New', $fixture[0]->getTitle());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getVideoId());
        self::assertSame('Something New', $fixture[0]->getPlaylist());
        self::assertSame('Something New', $fixture[0]->getCategories());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Formation();
        $fixture->setPublishedAt('My Title');
        $fixture->setTitle('My Title');
        $fixture->setDescription('My Title');
        $fixture->setVideoId('My Title');
        $fixture->setPlaylist('My Title');
        $fixture->setCategories('My Title');

        $this->repository->add($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/admin/formation/');
    }
}
