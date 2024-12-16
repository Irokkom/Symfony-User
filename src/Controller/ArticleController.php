<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface; // On importe EntityManagerInterface qui est utilisé pour interagir avec la base de données
use Symfony\Component\HttpFoundation\Response;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/article', name: 'article_')]
class ArticleController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository, EntityManagerInterface $entityManager): Response
    {
        // Récupérer toutes les catégories
        $categories = $entityManager->getRepository(Category::class)->findAll();
        
        // Récupérer tous les articles avec leurs auteurs
        $articles = $articleRepository->findAllWithAuthors();
    
        return $this->render('article/index.html.twig', [
            'articles' => $articles,
            'categories' => $categories, // Passer les catégories au template
        ]);
    }
    
    

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser(); // Récupérer l'utilisateur connecté
        $article = new Article();

        $form = $this->createFormBuilder($article)
            ->add('title', TextType::class)
            ->add('content', TextareaType::class)
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name', // Affiche le nom de la catégorie comme libellé
                'multiple' => true, // Permet de sélectionner plusieurs catégories
                'expanded' => false, // Champ de type liste déroulante ou multi-sélection
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setAuthor($user); // Associer l'auteur à l'utilisateur connecté
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('article_index');
        }

        return $this->render('/article/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Article $article): Response
    {
        return $this->render('/article/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/category/{id}', name: 'by_category', methods: ['GET'])]
    public function articlesByCategory(Category $category, ArticleRepository $articleRepository, EntityManagerInterface $entityManager): Response
    {
        $categories = $entityManager->getRepository(Category::class)->findAll();
        $articles = $articleRepository->findByCategory($category);
    
        return $this->render('article/index.html.twig', [
            'articles' => $articles,
            'categories' => $categories,
            'currentCategory' => $category, // Passe la catégorie sélectionnée
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder($article)
            ->add('title', TextType::class)
            ->add('content', TextareaType::class)
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => false,
            ])
            ->add('save', SubmitType::class, ['label' => 'Update Article'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('article_index');
        }

        return $this->render('/article/edit.html.twig', [
            'form' => $form->createView(),
            'article' => $article, // Passe l'article au template
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $article->getId(), $request->request->get('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('article_index');
    }
}
