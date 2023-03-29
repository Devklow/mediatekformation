<?php
namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controleur des formations
 *
 * @author emds
 */
class FormationsController extends AbstractController
{

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

    const PAGEFORMATION = "pages/formations.html.twig";
    const PAGE_FORMATION_ADMIN = "admin_formation/index.html.twig";

    public function __construct(FormationRepository $formationRepository, CategorieRepository $categorieRepository)
    {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository= $categorieRepository;
    }
    
    /**
     * @Route("/formations", name="formations")
     * @return Response
     */
    public function index(): Response
    {
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        return $this->render($this::PAGEFORMATION, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/formations/tri/{champ}/{ordre}/{table}", name="formations.sort")
     * @Route("/admin/formations/tri/{champ}/{ordre}/{table}", name="admin.formations.sort")
     * @param type $champ
     * @param type $ordre
     * @param type $table
     * @return Response
     */
    public function sort(Request $request,$champ, $ordre, $table=""): Response
    {
        if ($table==="") {
            $formations = $this->formationRepository->findAllOrderBy($champ, $ordre);
        } else {
            $formations = $this->formationRepository->findAllOrderByInTable($champ, $ordre, $table);
        }
        $categories = $this->categorieRepository->findAll();
        if ($request->get('_route') === "admin.formations.sort") {
            return $this->render($this::PAGE_FORMATION_ADMIN, [
                'formations' => $formations,
                'categories' => $categories,
            ]);
        }
        return $this->render($this::PAGEFORMATION, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }
    
    /**
     * @Route("/formations/recherche/{champ}/{table}", name="formations.findallcontain")
     * @Route("/admin/formations/recherche/{champ}/{table}", name="admin.formations.findallcontain")
     * @param type $champ
     * @param Request $request
     * @param type $table
     * @return Response
     */
    public function findAllContain($champ, Request $request, $table=""): Response
    {
        $valeur = $request->get("recherche");
        if ($valeur=="") {
            $formations = $this->formationRepository->findAll();
        } elseif ($table=="") {
            $formations = $this->formationRepository->findByContainValue($champ, $valeur);
        } else {
            $formations = $this->formationRepository->findByContainValueInTable($champ, $valeur, $table);
        }
        $categories = $this->categorieRepository->findAll();
        if ($request->get('_route') === "admin.formations.findallcontain") {
            return $this->render($this::PAGE_FORMATION_ADMIN, [
                'formations' => $formations,
                'categories' => $categories,
                'valeur' => $valeur,
                'table' => $table
            ]);
        }
        return $this->render($this::PAGEFORMATION, [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }

    /**
     * @Route("/formations/formation/{id}", name="formations.showone")
     * @param type $id
     * @return Response
     */
    public function showOne($id): Response
    {
        $formation = $this->formationRepository->find($id);
        return $this->render("pages/formation.html.twig", [
            'formation' => $formation
        ]);
    }
    
}
