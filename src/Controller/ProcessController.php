<?php

namespace App\Controller;

use App\Entity\Process;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProcessController extends AbstractController
{
    #[Route('/process', name: 'add_process')]
    public function index(ManagerRegistry $doctrine): Response
    {

//        $entityManager=$doctrine->getManager();
//        $process=new Process();
//        $process->setCpuNeed(2)->setRamNeed(4)->setIdMachine(1);
//        $entityManager->persist($process);
//        $entityManager->flush();

        $entityManager=$doctrine->getRepository(Process::class)->findAll();


        return $this->render('process/index.html.twig', [
            'controller_name' => 'ProcessController',
            'processes' => $entityManager,
        ]);
    }

    #[Route('/process/{id}', name: 'show_process')]
    public function show_process(ManagerRegistry $doctrine, $id): Response
    {

//

        $entityManager=$doctrine->getRepository(Process::class)->findBy(['id'=>$id]);


        return $this->render('process/show.html.twig', [
            'controller_name' => 'ProcessController',
            'processes' => $entityManager,
        ]);
    }
}
