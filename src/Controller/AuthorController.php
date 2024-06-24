<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Form\EditionAuteurType;

use App\Repository\AuthorRepository;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }

    #[Route('/author/list', name: 'list_author')]
    public function liste(EntityManagerInterface $entityManager): Response
    {
        $authors = $entityManager->getRepository(Author::class)->findAll();

        return $this->render('author/lister.html.twig', [
            'authors' => $authors,
        ]);
    }

    #[Route('/author/add', name: 'add_author')]
    public function ajouter(EntityManagerInterface $entityManager): Response
    {
        if ($_POST['enregistrer'] ?? false) {
            $author = new Author();
            $author->setName($_POST['name']);
            $entityManager->persist($author);
            $entityManager->flush();
            return $this->redirectToRoute('list_author');
        }
        return $this->render('author/ajout.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }

    #[Route('/author/delete/{id}', name: 'delete_author')]
    public function supprimer($id, EntityManagerInterface $entityManager): Response
    {
        $author = $entityManager->getRepository(Author::class)->find($id);
        if (!$author) {
            throw $this->createNotFoundException('Aucun auteur trouvé');
        }

        // Supprimer les livres de l'auteur
        $books = $entityManager->getRepository(Book::class)->findBy(['author' => $author]);
        foreach ($books as $book) {
            $entityManager->remove($book);
        }

        // Supprimer l'auteur
        $entityManager->remove($author);
        $entityManager->flush();

        return $this->redirectToRoute('list_author');
    }

  



    
}


