<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class IngredientController extends AbstractController
{
    #[Route('/ingredient', name: 'app_ingredient')]

    /**
     * This method display all ingredients
     * @param IngredientRepository $ingredientRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(IngredientRepository $ingredientRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $ingredients = $paginator->paginate(
            $ingredientRepository->findAllQueries(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/ingredient/index.html.twig', [
            'ingredients' => $ingredients,
        ]);
    }

    /**
     * This controller show the form to add an ingredient
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/ingredient/nouveau', name: 'app_ingredient_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();
            $em->persist($ingredient);
            $em->flush();

            $this->addFlash(
                'success',
                'Votre ingrédient a été créé avec succès !'
            );

            return $this->redirectToRoute('app_ingredient');
        }

        return $this->render('pages/ingredient/new.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/ingredient/edition/{id}', 'app_ingredient_edit', methods:['GET', ['POST']] )]
    public function edit(): Response
    {
        return $this->render('pages/ingredient/edit.html.twig');
    }

}
