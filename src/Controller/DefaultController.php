<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Vote;
use App\Repository\VoteRepository;
//use http\Env\Request;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit\Util\Json;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Post;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class DefaultController extends Controller
{
    protected function render(string $view, array $parameters = array(), Response $response = null): Response
    {
        $commonData = [
            'lastUserVotes' => $this->getDoctrine()->getRepository(Vote::class)->getLastUserVote($this->getUser()),
        ];
        return parent::render($view, $parameters + $commonData, $response);
    }

    /**
     * @Route("/", name="homepage")
     */
    public function index()
    {
        $posts = $this->getDoctrine()->getRepository(Post::class)->getTwoRandomElem();
        /** @var VoteRepository $repo */
        $repo = $this->getDoctrine()->getRepository(Vote::class);
        $lastVotes = $repo->getLastTenVote();
        $param = [
            'controller_name' => 'DefaultController',
            'posts' => $posts,
            'lastVotes' => $lastVotes
        ];
        return $this->render('default/index.html.twig', $param);
    }

    /**
     * @Route("/presentation", name="presentation")
     */
    public function presentation()
    {
        return $this->render('default/presentation.html.twig');
    }

    /**
     * @Route("/ajax/vote/{id}", name="vote")
     * @param Post $post
     * @return string
     */
    public function votePour(Post $post)
    {
        if ($this->getUser()) {

            $em = $this->getDoctrine()->getManager();

            $vote = new Vote();
            $vote->setDate(new \DateTime('now'));
            $vote->setPost($post);
            $vote->setUser($this->getUser());

            $em->persist($vote);
            $em->flush();

            return $this->json(['etat' => 'conf', 'message' => 'Votre vote a bien été pris en compte']);
        } else {
            return $this->json(['etat' => 'err', 'message' => 'Vous devez vous connecter pour pouvoir voter']);
        }
    }

    /**
     * @Route("/search", name="searchhp", defaults={"word" = false})
     * @Route("/search/{word}", name="search")
     */
    public function searchAction(Request $request, $word = '')
    {
        $news = [];
        if ($word) {
            $news = $this->getDoctrine()->getRepository(Post::class)->search($word);
        }
        $searchForm = $this->createFormBuilder()
            ->add('word', TextType::class, ['label' => 'Recherche'])
            ->add('submit', SubmitType::class, ['label' => 'Envoyer'])
            ->getForm();
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $data = $searchForm->getData();
            $news = $this->getDoctrine()->getRepository(Post::class)->search($data['word']);
        }
        // replace this example code with whatever you need
        return $this->render('default/search.html.twig', [
            'news' => $news,
            'form' => $searchForm->createView(),
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @param $slug
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/details/{slug}", name="details")
     */
    public function details(Request $request, $slug)
    {
        $post = $this->getDoctrine()->getRepository(Post::class)->findOneBy(['name' => $slug]);
        return $this->render('default/details.html.twig', [
            'post' => $post
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/categories/liste", name="categoriesList")
     */
    public function categoriesList(Request $request)
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        return $this->render('default/categories.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @param Request $request
     * @param $slug
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/categories/top/{slug}", name="categoryTop")
     */
    public function topCategory(Request $request, $slug)
    {
        /** @var Category $category */
        $category = $this->getDoctrine()->getRepository(Category::class)->findOneBy(['slug' => $slug]);
        $posts = $category->getPosts()->toArray();
        usort($posts, function($a, $b) {
            /** @var Post $a */
            /** @var Post $b */
            return  $b->getNbVote() <=> $a->getNbVote() ;
        });

        $randomPosts = $this->getDoctrine()->getRepository(Post::class)->getTwoRandomElem($category);

        return $this->render('default/topCategory.html.twig', [
            'category' => $category,
            'posts' => $posts,
            'randomPosts' => $randomPosts
        ]);
    }
}
