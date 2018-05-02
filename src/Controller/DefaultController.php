<?php

namespace App\Controller;

use http\Env\Request;
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
     * @Route("/ajax/vote", name="vote")
     */
    public function votePour()
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Post $post */
        $post = $em->getRepository(Post::class)->find($_POST['id_post']);
        if (!in_array($this->getUser(), $post->getUsers()->toArray())) {
            $post->setNbVote($post->getNbVote() + 1);
            $post->addUser($this->getUser());
            $em->persist($post);
            $em->flush();
            return $this->json(['etat' => 'conf', 'message' => 'Votre vote a bien été pris en compte']);
        } else {
            return $this->json(['etat' => 'err', 'message' => 'Vous avez déjà voté pour cette image']);
        }
    }
}
