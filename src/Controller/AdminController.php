<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Form\ArticlesAdminType;
use App\Repository\ArticlesRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/admin')]
#[IsGranted("ROLE_ADMIN")]

class AdminController extends AbstractController
{
    #[Route('/', name: 'app_admin_index', methods: ['GET'])]

    public function index(ArticlesRepository $articlesRepository): Response
    {
        return $this->render('admin/index.html.twig', [
            'articles' => $articlesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ArticlesRepository $articlesRepository, Security $security): Response
    {
        $article = new Articles();
        $user = $security->getUser();
        $article->setUser($user);
        $form = $this->createForm(ArticlesAdminType::class, $article);
        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('illustration')->getData();
            if($file){
                $originalNameFile = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $newFileName = $originalNameFile.uniqid(). '.' . $file->guessExtension();
                $file->move($this->getParameter('upload_directory') , $newFileName);

                $article->setIllustration($newFileName);
            }
        
            $articlesRepository->save($article, true);

            return $this->redirectToRoute('app_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_show', methods: ['GET'])]
    public function show(Articles $article): Response
    {
        return $this->render('admin/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Articles $article, ArticlesRepository $articlesRepository): Response
    {
        $form = $this->createForm(ArticlesAdminType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('illustration')->getData();
            if ($file){
                if ($article->getIllustration()){
                    $oldPathFile = $this->getParameter('upload_directory') . '/' . $article->getIllustration();
                    if(file_exists($oldPathFile)){
                        unlink($oldPathFile);
                    }
                }
                $originalNameFile = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $newFileName = $originalNameFile.uniqid(). '.' . $file->guessExtension();
                $file->move($this->getParameter('upload_directory') , $newFileName);

                $article->setIllustration($newFileName);
            }
            $articlesRepository->save($article, true);


            return $this->redirectToRoute('app_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_delete', methods: ['POST'])]
    public function delete(Request $request, Articles $article, ArticlesRepository $articlesRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $articlesRepository->remove($article, true);
        }

        return $this->redirectToRoute('app_admin_index', [], Response::HTTP_SEE_OTHER);
    }
}
