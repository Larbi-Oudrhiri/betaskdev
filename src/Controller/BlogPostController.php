<?php

namespace App\Controller;

use App\Entity\BlogPost;
use App\Form\BlogPostFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogPostController extends AbstractController
{
    #[Route('/user/blog/post', name: 'app_blog_post')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $blogPost = new BlogPost();
        $form = $this->createForm(BlogPostFormType::class, $blogPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $blogPost->setCreatedAt(new \DateTimeImmutable());
            $blogPost->setUser($this->getUser());

            $entityManager->persist($blogPost);
            $entityManager->flush();
        }

        $blogPosts = $entityManager->getRepository('App:BlogPost')->findAll();

        return $this->render('blog_post/index.html.twig', [
            'blogPosts' => $blogPosts,
            'blogpostForm' => $form->createView()
        ]);
    }

    #[Route('/user/blog/post/edit/{id}', name: 'app_blog_post_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, $id): Response
    {
        $blogPost = $entityManager->getRepository('App:BlogPost')->findOneById($id);
        $form = $this->createForm(BlogPostFormType::class, $blogPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($blogPost);
            $entityManager->flush();

            return $this->redirectToRoute('app_blog_post');
        }

        return $this->render('blog_post/edit.html.twig', [
            'blogpostForm' => $form->createView()
        ]);
    }
}
