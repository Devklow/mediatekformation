<?php
namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Description of PlaylistsController
 *
 * @author emds
 */
class PlaylistsController extends AbstractController
{
    const PAGES_PLAYLISTS_HTML_TWIG = "pages/playlists.html.twig";
    const PAGES_PLAYLISTS_ADMIN = "admin_playlist/index.html.twig";

    /**
     *
     * @var PlaylistRepository
     */
    private $playlistRepository;
    
    /**
     *
     * @var FormationRepository
     */
    private $formationRepository;
    
    /**
     *
     * @var CategorieRepository
     */
    private $categorieRepository;
    
    public function __construct(
        PlaylistRepository $playlistRepository,
        CategorieRepository $categorieRepository,
        FormationRepository $formationRespository
    )
    {
        $this->playlistRepository = $playlistRepository;
        $this->categorieRepository = $categorieRepository;
        $this->formationRepository = $formationRespository;
    }
    
    /**
     * @Route("/playlists", name="playlists")
     * @return Response
     */
    public function index(): Response
    {
        $playlists = $this->playlistRepository->findAllOrderByName('ASC');
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::PAGES_PLAYLISTS_HTML_TWIG, [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/playlists/tri/{champ}/{ordre}", name="playlists.sort")
     * @Route("/admin/playlists/tri/{champ}/{ordre}", name="admin.playlists.sort")
     * @param type $champ
     * @param type $ordre
     * @return Response
     */
    public function sort(Request $request, $champ, $ordre): Response
    {
        switch ($champ) {
            case "name":
                $playlists = $this->playlistRepository->findAllOrderByName($ordre);
                break;
            case "nbformations":
                $playlists = $this->playlistRepository->findAllOrderByNbFormations($ordre);
                break;
            default:
                $playlists = $this->playlistRepository->findall();
                break;
        }
        $categories = $this->categorieRepository->findAll();

        if ($request->get('_route')==="admin.playlists.sort") {
            return $this->render(self::PAGES_PLAYLISTS_ADMIN, [
                'playlists' => $playlists,
                'categories' => $categories
            ]);
        }
        return $this->render(self::PAGES_PLAYLISTS_HTML_TWIG, [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/playlists/recherche/{champ}/{table}", name="playlists.findallcontain")
     * @Route("/admin/playlists/recherche/{champ}/{table}", name="admin.playlists.findallcontain")
     * @param type $champ
     * @param Request $request
     * @param type $table
     * @return Response
     */
    public function findAllContain(Request $request, $champ, $table=""): Response
    {
        $valeur = $request->get("recherche");
        if ($valeur=="") {
            $playlists = $this->playlistRepository->findAllOrderByName('ASC');
        } elseif($table=="") {
            $playlists = $this->playlistRepository->findByContainValue($champ, $valeur);
        } else {
            $playlists = $this->playlistRepository->findByCategorie($champ, $valeur);
        }
        $categories = $this->categorieRepository->findAll();
        if ($request->get('_route')==="admin.playlists.findallcontain") {
            return $this->render(self::PAGES_PLAYLISTS_ADMIN, [
                'playlists' => $playlists,
                'categories' => $categories,
                'valeur' => $valeur,
                'table' => $table
            ]);
        }
        return $this->render(self::PAGES_PLAYLISTS_HTML_TWIG, [
            'playlists' => $playlists,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }
    
    /**
     * @Route("/playlists/playlist/{id}", name="playlists.showone")
     * @param type $id
     * @return Response
     */
    public function showOne($id): Response
    {
        $playlist = $this->playlistRepository->find($id);
        $playlistCategories = $this->categorieRepository->findAllForOnePlaylist($id);
        $playlistFormations = $this->formationRepository->findAllForOnePlaylist($id);
        return $this->render("pages/playlist.html.twig", [
            'playlist' => $playlist,
            'playlistcategories' => $playlistCategories,
            'playlistformations' => $playlistFormations
        ]);
    }

}
