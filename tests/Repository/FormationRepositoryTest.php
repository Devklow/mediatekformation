<?php

namespace App\Tests\Repository;

use App\Entity\Formation;
use App\Entity\Playlist;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FormationRepositoryTest extends KernelTestCase
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testFindAllForOnePlaylist()
    {
        $playlistRepository = $this->entityManager->getRepository(Playlist::class);
        $formationRepository = $this->entityManager->getRepository(Formation::class);

        $playlist = new Playlist();
        $playlist->setName('playlist');
        $playlistRepository->add($playlist, true);
        $playlist = $playlistRepository->findOneBy(['name'=>$playlist->getName()]);
        $playlist2 = new Playlist();
        $playlist2->setName('playlist2');
        $playlistRepository->add($playlist2, true);
        $playlist2 = $playlistRepository->findOneBy(['name'=>$playlist2->getName()]);

        $formation1 = new Formation();
        $formation1->setTitle('form1')->setPublishedAt(new \DateTime('now'))->setPlaylist($playlist);
        $formationRepository->add($formation1, true);
        $formation2 = new Formation();
        $formation2->setTitle('form2')->setPublishedAt(new \DateTime('now'))->setPlaylist($playlist);
        $formationRepository->add($formation2, true);
        $formation3 = new Formation();
        $formation3->setTitle('form3')->setPublishedAt(new \DateTime('now'))->setPlaylist($playlist2);
        $formationRepository->add($formation3, true);

        $find1 = $formationRepository->FindAllForOnePlaylist($playlist->getId());
        $find2 = $formationRepository->FindAllForOnePlaylist($playlist2->getId());

        $formationRepository->remove($formation1, true);
        $formationRepository->remove($formation2, true);
        $formationRepository->remove($formation3, true);
        $playlistRepository->remove($playlist, true);
        $playlistRepository->remove($playlist2, true);

        self::assertSame(2, count($find1));
        self::assertSame(1, count($find2));

    }

    public function testFindAllLasted()
    {
        $playlistRepository = $this->entityManager->getRepository(Playlist::class);
        $formationRepository = $this->entityManager->getRepository(Formation::class);

        $playlist = new Playlist();
        $playlist->setName('playlist');
        $playlistRepository->add($playlist, true);

        $formation1 = new Formation();
        $formation1->setTitle('form1')->setPublishedAt(new \DateTime('2021-01-04 17:00:12'))->setPlaylist($playlist);
        $formationRepository->add($formation1, true);
        $formation2 = new Formation();
        $formation2->setTitle('form2')->setPublishedAt(new \DateTime('2022-01-04 17:00:12'))->setPlaylist($playlist);
        $formationRepository->add($formation2, true);
        $formation3 = new Formation();
        $formation3->setTitle('form3')->setPublishedAt(new \DateTime('now'))->setPlaylist($playlist);
        $formationRepository->add($formation3, true);

        $find = $formationRepository->FindAllLasted(2);

        $formationRepository->remove($formation1, true);
        $formationRepository->remove($formation2, true);
        $formationRepository->remove($formation3, true);
        $playlistRepository->remove($playlist, true);

        self::assertSame($formation3, $find[0]);
        self::assertSame($formation2, $find[1]);
        self::assertSame(2, count($find));

    }

    public function testFindAllOrderBy()
    {
        $playlistRepository = $this->entityManager->getRepository(Playlist::class);
        $formationRepository = $this->entityManager->getRepository(Formation::class);

        $playlist = new Playlist();
        $playlist->setName('playlist');
        $playlistRepository->add($playlist, true);

        $formation1 = new Formation();
        $formation1->setTitle('a_first')->setPublishedAt(new \DateTime('2021-01-04 17:00:12'))->setPlaylist($playlist);
        $formationRepository->add($formation1, true);
        $formation2 = new Formation();
        $formation2->setTitle('b_second')->setPublishedAt(new \DateTime('2022-01-04 17:00:12'))->setPlaylist($playlist);
        $formationRepository->add($formation2, true);

        $find1 = $formationRepository->FindAllOrderBy('title','ASC');
        $find2 = $formationRepository->FindAllOrderBy('publishedAt', 'DESC');

        $formationRepository->remove($formation1, true);
        $formationRepository->remove($formation2, true);
        $playlistRepository->remove($playlist, true);

        self::assertSame($formation1, $find1[0]);
        self::assertSame($formation2, $find1[1]);
        self::assertSame($formation1, $find2[1]);
        self::assertSame($formation2, $find2[0]);

    }

    public function testFindByContainValue()
    {
        $playlistRepository = $this->entityManager->getRepository(Playlist::class);
        $formationRepository = $this->entityManager->getRepository(Formation::class);

        $playlist = new Playlist();
        $playlist->setName('playlist');
        $playlistRepository->add($playlist, true);

        $formation1 = new Formation();
        $formation1->setTitle('containSomething')->setPublishedAt(new \DateTime('now'))->setPlaylist($playlist);
        $formationRepository->add($formation1, true);
        $formation2 = new Formation();
        $formation2->setTitle('containSomethingElse')->setPublishedAt(new \DateTime('now'))->setPlaylist($playlist);
        $formationRepository->add($formation2, true);

        $find1 = $formationRepository->FindByContainValue('title','containSomething');
        $find2 = $formationRepository->FindByContainValue('title','containSomethingElse');
        $find3 = $formationRepository->FindByContainValue('title','findNothing');

        $formationRepository->remove($formation1, true);
        $formationRepository->remove($formation2, true);
        $playlistRepository->remove($playlist, true);

        self::assertSame(2, count($find1));
        self::assertSame(1, count($find2));
        self::assertSame(0, count($find3));

    }

        public function testFindByContainValueInTable()
    {
        $playlistRepository = $this->entityManager->getRepository(Playlist::class);
        $formationRepository = $this->entityManager->getRepository(Formation::class);

        $playlist = new Playlist();
        $playlist->setName('playlist')->setDescription('ContainSomething');
        $playlistRepository->add($playlist, true);
        $playlist2 = new Playlist();
        $playlist2->setName('playlist2')->setDescription('ContainSomethingElse');
        $playlistRepository->add($playlist2, true);

        $formation1 = new Formation();
        $formation1->setTitle('Form1')->setPublishedAt(new \DateTime('now'))->setPlaylist($playlist);
        $formationRepository->add($formation1, true);
        $formation2 = new Formation();
        $formation2->setTitle('Form2')->setPublishedAt(new \DateTime('now'))->setPlaylist($playlist2);
        $formationRepository->add($formation2, true);

        $find1 = $formationRepository->FindByContainValueInTable('description','containSomething', 'playlist');
        $find2 = $formationRepository->FindByContainValueInTable('description','containSomethingElse','playlist');
        $find3 = $formationRepository->FindByContainValueInTable('description','findNothing','playlist');

        $formationRepository->remove($formation1, true);
        $formationRepository->remove($formation2, true);
        $playlistRepository->remove($playlist, true);
        $playlistRepository->remove($playlist2, true);

        self::assertSame(2, count($find1));
        self::assertSame(1, count($find2));
        self::assertSame(0, count($find3));

    }

    public function testFindAllOrderByInTable()
    {
        $playlistRepository = $this->entityManager->getRepository(Playlist::class);
        $formationRepository = $this->entityManager->getRepository(Formation::class);

        $playlist = new Playlist();
        $playlist->setName('playlist')->setDescription('a_first');
        $playlistRepository->add($playlist, true);
        $playlist2 = new Playlist();
        $playlist2->setName('playlist2')->setDescription('b_second');
        $playlistRepository->add($playlist2, true);

        $formation1 = new Formation();
        $formation1->setTitle('Form1')->setPublishedAt(new \DateTime('now'))->setPlaylist($playlist);
        $formationRepository->add($formation1, true);
        $formation2 = new Formation();
        $formation2->setTitle('Form2')->setPublishedAt(new \DateTime('now'))->setPlaylist($playlist2);
        $formationRepository->add($formation2, true);

        $find1 = $formationRepository->FindAllOrderByInTable('description','ASC', 'playlist');
        $find2 = $formationRepository->FindAllOrderByInTable('description','DESC','playlist');

        $formationRepository->remove($formation1, true);
        $formationRepository->remove($formation2, true);
        $playlistRepository->remove($playlist, true);
        $playlistRepository->remove($playlist2, true);

        self::assertSame($formation1, $find1[0]);
        self::assertSame($formation2, $find1[1]);
        self::assertSame($formation1, $find2[1]);
        self::assertSame($formation2, $find2[0]);
    }
}
