<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/categories", name="admin.categories.")
 */
class AdminCategorieController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET", "POST"})
     */
    public function index(Request $request, CategorieRepository $categorieRepository): Response
    {
        $newCategorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $newCategorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($categorieRepository->findBy(['name'=>$newCategorie->getName()])){
                $this->addFlash('error', "Vous ne pouvez ajouter une catégorie de même nom qu'une existante");
            }
            else{
                $categorieRepository->add($newCategorie, true);
                $this->addFlash('success', 'Catégorie '.$newCategorie.' ajoutée');
            }
        }

        return $this->renderForm('admin_categorie/index.html.twig', [
            'newCategorie' => $newCategorie,
            'categories' => $categorieRepository->findAll(),
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Categorie $categorie, CategorieRepository $categorieRepository): Response
    {
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categorieRepository->add($categorie, true);

            return $this->redirectToRoute('admin.categories.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_categorie/edit.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"POST"})
     */
    public function delete(Request $request, Categorie $categorie, CategorieRepository $categorieRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categorie->getId(), $request->request->get('_token'))) {
            if (!$categorie->getFormations()->isEmpty()) {
                $this->addFlash('error', 'La catégorie '.$categorie.' ne peut être supprimée');
            } else {
                $categorieRepository->remove($categorie, true);
                $this->addFlash('success', 'La catégorie '.$categorie.' a été supprimée');
            }
        }
        return $this->redirectToRoute('admin.categories.index', [], Response::HTTP_SEE_OTHER);
    }
}
