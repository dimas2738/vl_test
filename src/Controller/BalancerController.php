<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BalancerController extends AbstractController
{
    #[Route('/', name: 'app_balancer')]
    public function index(): Response
    {
        return $this->render('balancer/index.html.twig', [
            'controller_name' => 'BalancerController',
        ]);
    }
}
