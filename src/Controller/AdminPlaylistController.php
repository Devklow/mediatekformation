<?php

namespace App\Controller;

use App\Entity\Playlist;
use App\Form\PlaylistType;
use App\Repository\CategorieRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/playlists", name="admin.playlists.")
 */
class AdminPlaylistController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(PlaylistRepository $playlistRepository, CategorieRepository $categorieRepository): Response
    {
        return $this->render('admin_playlist/index.html.twig', [
            'playlists' => $playlistRepository->findAll(),
            'categories' => $categorieRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     */
    public function new(Request $request, PlaylistRepository $playlistRepository): Response
    {
        $playlist = new Playlist();
        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $playlistRepository->add($playlist, true);

            return $this->redirectToRoute('admin.playlists.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_playlist/new.html.twig', [
            'playlist' => $playlist,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Playlist $playlist, PlaylistRepository $playlistRepository): Response
    {
        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $playlistRepository->add($playlist, true);

            return $this->redirectToRoute('admin.playlists.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_playlist/edit.html.twig', [
            'playlist' => $playlist,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"POST"})
     */
    public function delete(Request $request, Playlist $playlist, PlaylistRepository $playlistRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$playlist->getId(), $request->request->get('_token'))) {
            if (!$playlist->getFormations()->isEmpty()) {
                $this->addFlash('error','Impossible de supprimer une Playlist contenant des formations');
                return $this->redirectToRoute('admin.playlists.index', [], Response::HTTP_SEE_OTHER);
            }
            $playlistRepository->remove($playlist, true);
        }

        return $this->redirectToRoute('admin.playlists.index', [], Response::HTTP_SEE_OTHER);
    }
}
