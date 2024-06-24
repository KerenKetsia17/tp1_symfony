<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use App\Repository\AuthorRepository;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use App\Entity\Author;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book', methods: ['GET'])]
    public function index(BookRepository $bookRepository): Response
    {
        return $this->render('book/index.html.twig', [
            'books' => $bookRepository->findAll(),
        ]);
    }


    //ajouter
    #[Route('/book/add', name: 'add_book')]
    public function ajouter(Request $request, EntityManagerInterface $entityManager, AuthorRepository $authorRepository): Response
    {
        $authorCount = $authorRepository->countAuthors();

        if ($authorCount === 0) {
            return $this->redirectToRoute('add_author');
        }

        if ($request->isMethod('POST')) {
            $book = new Book();
            $book->setName($request->request->get('name'));

            $authorId = $request->request->get('author_id');
            $author = $entityManager->getRepository(Author::class)->find($authorId);

            if ($author) {
                $book->setAuthor($author);
                $entityManager->persist($book);
                $entityManager->flush();

                return $this->redirectToRoute('list_author');
            }
        }

        $authors = $entityManager->getRepository(Author::class)->findAll();

        return $this->render('book/add.html.twig', [
            'authors' => $authors,
        ]);
    }


    //afficher
    #[Route('/book/list/{id}', name: 'list_book')]
    public function liste($id,EntityManagerInterface $entityManager): Response
    {
        $author = $entityManager->getRepository(Author::class)->find($id);
        if (!$author) {
            throw $this->createNotFoundException('Auteur non trouvé');
        }

        $books = $entityManager->getRepository(Book::class)->findBy(['author' => $author]);

        return $this->render('book/lister.html.twig', [
            'author' => $author,
            'books' => $books,
        ]);
    }

    //supprimer
    #[Route('/book/delete/{id}', name: 'delete_book')]
    public function supprimer($id, EntityManagerInterface $entityManager): Response
    {
        $book = $entityManager->getRepository(Book::class)->find($id);
        if (!$book) {
            throw $this->createNotFoundException('Aucun livre trouvé');
        }

        $entityManager->remove($book);
        $entityManager->flush();

        return $this->redirectToRoute('list_book', ['id' => $book->getAuthor()->getId()]);
    }

    // //modifier les livres
    #[Route('/book/update/{id}', name: 'update_book', methods: ['POST'])]
    public function update($id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $book = $entityManager->getRepository(Book::class)->find($id);
        if (!$book) {
            throw $this->createNotFoundException('Le livre n\'a pas été trouvé');
        }

        $bookName = $request->request->get('bookName');
        if ($bookName) {
            $book->setName($bookName);
            $entityManager->flush();
            $this->addFlash('success', 'Le livre a été mis à jour avec succès.');
        }

        return $this->redirectToRoute('list_book', ['id' => $book->getAuthor()->getId()]);
    }

}

