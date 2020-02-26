<?php

namespace App\Controller\etudiant;

use App\Repository\PostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    //FONCTION POUR AFFICHER BLOG
    /**
     * @Route("/etudiant/blog", name="blog")
     */
    public function index(Request $request,PaginatorInterface $paginator,PostRepository $repo)
    {
        $article = $paginator->paginate(
            $repo->findAll(),
            $request->query->getInt('page', 1),
            6
        );

        //$article = $repo->findAll();
        return $this->render('blog/index.html.twig', [
            'articles' => $article,
        ]);
    }
}
