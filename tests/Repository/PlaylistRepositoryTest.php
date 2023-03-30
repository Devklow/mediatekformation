<?php

namespace App\Tests\Repository;

use App\Entity\Categorie;
use App\Entity\Formation;
use App\Entity\Playlist;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PlaylistRepositoryTest extends KernelTestCase
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


    public function testFindByContainValue()
    {
        $playlist1 = new Playlist();
        $playlist2 = new Playlist();
        $playlistRepository = $this->entityManager->getRepository(Playlist::class);
        $playlist1->setName("premier");
        $playlistRepository->add($playlist1, true);
        $playlist2->setName("deuxième");
        $playlistRepository->add($playlist2, true);
        $find = $playlistRepository->findByContainValue("name","premier");
        self::assertSame($playlist1, $find[0]);
        $playlistRepository->remove($playlist1, true);
        $find = $playlistRepository->findByContainValue("name","premier");
        self::assertSame(true, empty($find));
        self::assertSame($playlist2, $playlistRepository->findByContainValue("name","deux")[0]);
        $playlistRepository->remove($playlist2, true);
    }

    public function testFindAllOrderByName()
    {
        $playlist1 = new Playlist();
        $playlist2 = new Playlist();
        $playlistRepository = $this->entityManager->getRepository(Playlist::class);
        $playlist1->setName("a_first");
        $playlistRepository->add($playlist1, true);
        $playlist2->setName("b_second");
        $playlistRepository->add($playlist2, true);
        $find = $playlistRepository->FindAllOrderByName('ASC');
        $find2 = $playlistRepository->FindAllOrderByName('DESC');
        //Nettoyage des entités
        $playlistRepository->remove($playlist1, true);
        $playlistRepository->remove($playlist2, true);

        self::assertSame($playlist1, $find[0]);
        self::assertSame($playlist2, $find2[0]);
        self::assertSame(2, count($find2));
    }

    public function testFindByCategorie()
    {
        $playlistRepository = $this->entityManager->getRepository(Playlist::class);
        $formationRepository = $this->entityManager->getRepository(Formation::class);
        $categorieRepository = $this->entityManager->getRepository(Categorie::class);

        $playlist = new Playlist();
        $playlist->setName('playlist');
        $playlistRepository->add($playlist, true);
        $playlist2 = new Playlist();
        $playlist2->setName('playlist2');
        $playlistRepository->add($playlist2, true);

        $cat1 = new Categorie();
        $cat1->setName('cat1');
        $categorieRepository->add($cat1,true);
        $cat2 = new Categorie();
        $cat2->setName('cat2');
        $categorieRepository->add($cat2,true);
        $cat3 = new Categorie();
        $cat3->setName('cat3');
        $categorieRepository->add($cat3,true);

        $formation1 = new Formation();
        $formation1->setTitle('form1')->setPublishedAt(new \DateTime('now'))->addCategory($cat1)
            ->setPlaylist($playlist);
        $formationRepository->add($formation1, true);
        $formation2 = new Formation();
        $formation2->setTitle('form2')->setPublishedAt(new \DateTime('now'))->addCategory($cat2)
            ->setPlaylist($playlist);
        $formationRepository->add($formation2, true);
        $formation3 = new Formation();
        $formation3->setTitle('form3')->setPublishedAt(new \DateTime('now'))->addCategory($cat1)
            ->setPlaylist($playlist2);
        $formationRepository->add($formation3, true);

        self::assertSame(0, count($playlistRepository->FindByCategorie("name",'cat3')));
        self::assertSame(1, count($playlistRepository->FindByCategorie("name",'cat2')));
        self::assertSame(2, count($playlistRepository->FindByCategorie("name",'cat1')));
        $formationRepository->remove($formation1, true);
        $formationRepository->remove($formation2, true);
        $formationRepository->remove($formation3, true);
        $categorieRepository->remove($cat1, true);
        $categorieRepository->remove($cat2, true);
        $categorieRepository->remove($cat3, true);
        $playlistRepository->remove($playlist, true);
        $playlistRepository->remove($playlist2, true);
    }

    public function testFindAllOrderByNbFormations()
    {
        $playlistRepository = $this->entityManager->getRepository(Playlist::class);
        $formationRepository = $this->entityManager->getRepository(Formation::class);

        $playlist = new Playlist();
        $playlist->setName('playlist');
        $playlistRepository->add($playlist, true);
        $playlist2 = new Playlist();
        $playlist2->setName('playlist2');
        $playlistRepository->add($playlist2, true);

        $formation1 = new Formation();
        $formation1->setTitle('form1')->setPublishedAt(new \DateTime('now'))->setPlaylist($playlist);
        $formationRepository->add($formation1, true);
        $formation2 = new Formation();
        $formation2->setTitle('form2')->setPublishedAt(new \DateTime('now'))->setPlaylist($playlist);
        $formationRepository->add($formation2, true);
        $formation3 = new Formation();
        $formation3->setTitle('form3')->setPublishedAt(new \DateTime('now'))->setPlaylist($playlist2);
        $formationRepository->add($formation3, true);

        $asc = $playlistRepository->FindAllOrderByNbFormations('ASC');
        $desc = $playlistRepository->FindAllOrderByNbFormations('DESC');

        $formationRepository->remove($formation1, true);
        $formationRepository->remove($formation2, true);
        $formationRepository->remove($formation3, true);
        $playlistRepository->remove($playlist, true);
        $playlistRepository->remove($playlist2, true);


        self::assertSame($playlist, $asc[1]);
        self::assertSame($playlist2, $asc[0]);
        self::assertSame($playlist2, $desc[1]);
        self::assertSame($playlist, $desc[0]);


    }
}
