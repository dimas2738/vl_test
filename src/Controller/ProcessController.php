<?php

namespace App\Controller;

use App\Entity\Machine;
use App\Entity\Process;
use App\Form\AddMachineFormType;
use App\Form\AddProcessType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProcessController extends AbstractController
{
    #[Route('/process', name: 'all_processes')]
    public function index(ManagerRegistry $doctrine): Response
    {

        $entityManager=$doctrine->getRepository(Process::class)->findAll();

        return $this->render('process/index.html.twig', [
            'controller_name' => 'ProcessController',
            'processes' => $entityManager,
        ]);
    }

    #[Route('/process/{id}', name: 'show_process')]
    public function show_process(ManagerRegistry $doctrine, $id): Response
    {


        $entityManager=$doctrine->getRepository(Process::class)->findBy(['id'=>$id]);


        return $this->render('process/show.html.twig', [
            'controller_name' => 'ProcessController',
            'processes' => $entityManager,
        ]);
    }

    #[Route('/add_process', name: 'add_process')]
    public function add_process(Request $request, ManagerRegistry $doctrine): Response
    {




        $process = new Process();
        $form = $this->createForm(AddProcessType::class, $process);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ram_need = $form->getData()->getRamNeed();
            $cpu_need = $form->getData()->getCpuNeed();

            //Find machine for process
            $machine = $doctrine->getRepository(Machine::class)->createQueryBuilder('machine')
                ->andWhere("machine.cpu_remaind >= $cpu_need")->andWhere("machine.ram_remaind >=  $ram_need")
                ->orderBy('machine.cpu_remaind', 'DESC') ->setMaxResults( 1 )
            ->getQuery()->execute();



            $res=count($machine);
            if ($res<=0){
                return $this->render('process/show.html.twig', [
                    'controller_name' => 'ProcessController',
                    'error' => 'SORRY 
                     Our Machines can\'t Do this Process',
                ]);
            }

            else{
                $machine_for_process=$machine[0];
                $entityManager = $doctrine->getManager();
                $process->setRamNeed($ram_need)->setCpuNeed($cpu_need)->setMachine($machine_for_process);
                $entityManager->persist($process);
                $entityManager->flush();

                $entityManager=$doctrine->getManager();
                $machine_cpu_remaind=$machine_for_process->getCpuRemaind();
                $machine_ram_remaind=$machine_for_process->getRamRemaind();

                $machine_for_process->setCpuRemaind($machine_cpu_remaind-$cpu_need)->setRamRemaind($machine_ram_remaind-$ram_need);
                $entityManager->persist($machine_for_process);
                $entityManager->flush();


                return $this->redirect('/process');
            }


        }
        return $this->render('process/add.html.twig', array(
            'form' => $form->createView()
        ));
    }

    #[Route('/del_process/{id}', name: 'del_process')]
    public function del_process(ManagerRegistry $doctrine, $id): Response
    {
        //find process by id
        //select process cpu and ram
        $em = $doctrine->getManager();
        $entity = $doctrine->getRepository(Process::class)->findBy(['id' => $id]);
        $procees_ram=$entity[0]->getRamNeed();
        $procees_cpu=$entity[0]->getCpuNeed();


        //find machine belong to process
        //select machine remaind cpu and ram
        $machine_id=$entity[0]->getMachine()->getId();
        $machine = $doctrine->getRepository(Machine::class)->findBy(['id' => $machine_id]);
        $machine_ram=$machine[0]->getRamRemaind();
        $machine_cpu=$machine[0]->getCpuRemaind();

        //set summ of ram and ram_remaind & cpu and cpu_remaind
        //to machine remaind options
        $entityManager=$doctrine->getManager();
        $machine[0]->setRamRemaind($machine_ram+$procees_ram)->
        setCpuRemaind($machine_cpu+$procees_cpu);
        $entityManager->persist($machine[0]);
        $entityManager->flush();


        if ($entity != null) {
            foreach ($entity as $e) {
                $em->remove($e);
            }
            $em->flush();
        }
        return $this->redirect('/process');
    }

}
