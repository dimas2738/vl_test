<?php

namespace App\Controller;

use App\Entity\Machine;
use App\Entity\Process;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BalancerController extends AbstractController
{
    #[Route('/', name: 'app_balancer')]
    public function index(ManagerRegistry $doctrine): Response
    {
////        $entityMachine = $doctrine->getRepository(Machine::class)->findAll();
////        $entityProcess = $doctrine->getRepository(Process::class)->findAll();
//        $user = $doctrine
//            ->getEntityManager()
//            ->createQueryBuilder()
//            ->select('p')
//            ->from(Process::class, 'p')
//            ->join('u.languages', 'l_eng', 'WITH', 'l_eng.language = :engCode')
//            ->join('u.languages', 'l_fr', 'WITH', 'l_fr.language = :frCode')
//            ->setParameters([
//                'engCode' => 'english',
//                'frCode' => 'french'
//            ])
//            ->getQuery()->execute();


        $products = $doctrine->getManager()
            ->createQueryBuilder()
            ->select('p')
//            Process::class, 'p'
            ->from(Process::class, 'p')
//            ->leftJoin(Process::class, 'p', 'with', 'p.id = m.id')
////            ->where('cp.category = '.$category->getId())
//////            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult();
        dump($products);
//        die();

$num=3;

        $products2 = $doctrine->getManager()
            ->createQueryBuilder()
            ->select('m')
//            Process::class, 'p'
            ->from(Machine::class, 'm')
//            ->leftJoin(Process::class, 'p', 'with', 'p.id = m.id')
            ->where('m.id = '.$num)
//////            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult();

        dump($products2);
        die();
        return $this->render('balancer/index.html.twig', [
            'controller_name' => 'BalancerController',
        ]);
    }
}
