<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Reporter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin/reporters', name: 'reporters')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $reporters = $entityManager->getRepository(Reporter::class)->findAll();

        $data = [];
        foreach ($reporters as $reporter) {
            $data[] = [
                'firstName' => $reporter->getFirstName(),
                'lastName' => $reporter->getLastName(),
                'email' =>  $reporter->getEmail(),
                'views' => array_sum(array_map(function($article) {
                    return $article->getViews();
                }, $reporter->getArticles()))
            ];
        }

        return $this->render('admin/reporters.html.twig', [
            'reporters' => $data,
        ]);
    }

    #[Route('/admin', name: 'adminhome')]
    public function home(EntityManagerInterface $entityManager): Response
    {
        $articles = $entityManager->getRepository(Article::class)->findBy([], ['visitors' => 'DESC'], 6);

        $data = [];
        foreach ($articles as $article) {
            $data[] = [
                'id' => $article->getId(),
                'title' => $article->getTitle(),
                'content' => $article->getContent(),
                'visitors'=> $article->getVisitors(),
            ];
        }

        return $this->render('admin/index.html.twig', [
            'articles' => $data,
        ]);
    }

    #[Route('/admin/topreporter', name: 'topreporter')]
public function topReporter(EntityManagerInterface $entityManager): Response
{
    $reporters = $entityManager->getRepository(Reporter::class)->findAll();

    $topReporter = null;
    $maxViews = 0;

    foreach ($reporters as $reporter) {
        $views = array_sum(array_map(function ($article) {
            return $article->getViews();
        }, $reporter->getArticles()));

        if ($views > $maxViews) {
            $maxViews = $views;
            $topReporter = [
                'firstName' => $reporter->getFirstName(),
                'lastName' => $reporter->getLastName(),
                'email' => $reporter->getEmail(),
                'views' => $views,
            ];
        }
    }

    return $this->render('admin/topreporter.html.twig', [
        'topreporter' => $topReporter,
    ]);
}

}


