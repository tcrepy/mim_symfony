<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Post;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function index()
    {
        $posts = $this->getDoctrine()->getRepository(Post::class)->getTwoRandomElem();
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/presentation", name="presentation")
     */
    public function presentation()
    {
        return $this->render('default/presentation.html.twig');
    }

    /**
     * @param Post $post
     * @Route("/vote/{id}", name="vote")
     */
    public function votePour(Post $post)
    {

    }
}
