<?php

namespace App\Tests\Repository;

use App\Entity\Categorie;
use App\Entity\Formation;
use App\Entity\Playlist;
use App\Repository\CategorieRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CategorieRepositoryTest extends KernelTestCase
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
        $categorieRepository = $this->entityManager->getRepository(Categorie::class);

        $playlist = new Playlist();
        $playlist->setName('playlist');
        $playlistRepository->add($playlist, true);
        $playlist = $playlistRepository->findOneBy(['name'=>$playlist->getName()]);

        $playlist2 = new Playlist();
        $playlist2->setName('playlist2');
        $playlistRepository->add($playlist2, true);
        $playlist2 = $playlistRepository->findOneBy(['name'=>$playlist2->getName()]);

        $playlist3 = new Playlist();
        $playlist3->setName('playlist3');
        $playlistRepository->add($playlist3, true);
        $playlist3 = $playlistRepository->findOneBy(['name'=>$playlist3->getName()]);


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
            ->addCategory($cat3)
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

        $find1 = $categorieRepository->FindAllForOnePlaylist($playlist->getId());
        $find2 = $formationRepository->FindAllForOnePlaylist($playlist2->getId());
        $find3 = $formationRepository->FindAllForOnePlaylist($playlist3->getId());

        $formationRepository->remove($formation1, true);
        $formationRepository->remove($formation2, true);
        $formationRepository->remove($formation3, true);
        $categorieRepository->remove($cat1, true);
        $categorieRepository->remove($cat2, true);
        $categorieRepository->remove($cat3, true);
        $playlistRepository->remove($playlist, true);
        $playlistRepository->remove($playlist2, true);

        self::assertSame(3, count($find1));
        self::assertSame(1, count($find2));
        self::assertSame(0, count($find3));

    }
}
