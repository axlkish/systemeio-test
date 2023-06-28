<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\ProductsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MainController extends AbstractController
{
    /** @var ProductsRepository */
    public ProductsRepository $productsRepository;

    /**
     * @param UsersModel $usersModel
     */
    public function __construct(
        ProductsRepository $productsRepository
    )
    {
        $this->productsRepository = $productsRepository;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function main(Request $request): Response
    {
        $products = $this->productsRepository->findAll();
        return $this->render('main.html.twig', ['products' => $products]);
    }
}
