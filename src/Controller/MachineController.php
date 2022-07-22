<?php

namespace App\Controller;

use App\Entity\Machine;

use App\Form\AddMachineFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

class MachineController extends AbstractController
{
    #[Route('/all_machine', name: 'all_machine')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getRepository(Machine::class)->findAll();
        return $this->render('machine/index.html.twig', [
            'controller_name' => 'MachineController',
            'data' => $entityManager
        ]);
    }

    #[Route('/machine/{id}', name: 'show_machine')]
    public function show_machine(ManagerRegistry $doctrine, $id): Response
    {
        $entityManager = $doctrine->getRepository(Machine::class)->findBy(['id' => $id]);
        return
            $this->render('machine/show.html.twig', [
            'controller_name' => 'MachineController',
            'data' => $entityManager,
        ]);
    }

    #[Route('/add_machine', name: 'add_machine')]
    public function add_machine(Request $request, ManagerRegistry $doctrine): Response
    {
        $machine = new Machine();
        $form = $this->createForm(AddMachineFormType::class, $machine);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ram = $form->getData()->getRam();
            $cpu = $form->getData()->getCpu();


            $entityManager = $doctrine->getManager();
            $machine->setCpu($cpu)->setRam($ram)->setCpuRemaind($cpu)->setRamRemaind($ram);
            $entityManager->persist($machine);
            $entityManager->flush();
            return $this->redirect('/all_machine');
        }
        return $this->render('machine/add.html.twig', array(
            'form' => $form->createView()
        ));
    }

    #[Route('/del_machine/{id}', name: 'del_machine')]
    public function del_machine(ManagerRegistry $doctrine, $id): Response
    {
        $em = $doctrine->getManager();
        $entity = $doctrine->getRepository(Machine::class)->findBy(['id' => $id]);
        if ($entity != null) {
            foreach ($entity as $e) {
                $em->remove($e);
            }
            $em->flush();
        }
        return $this->redirect('/all_machine');
    }

    #[Route('/edit_machine/{id}', name: 'edit_machine')]
    public function edit_machine(Request $request, ManagerRegistry $doctrine,$id): Response
    {
        $customer = $doctrine->getRepository(Machine::class)->findOneBy(['id' => $id]);
////        $entityManager = $doctrine->getRepository(Machine::class)->findBy(['id' => $id]);
//        $machine = new Machine();

//        dump($customer);die();
        $form = $this->createForm(AddMachineFormType::class, $customer);
        $form->handleRequest($request);
//        dump($form);
        if ($form->isSubmitted() && $form->isValid()) {
            $ram = $form->getData()->getRam();
            $cpu = $form->getData()->getCpu();

            $cr=$form->getData()->getCpuRemaind();
            $cpu_remaind= $cr+ ($cpu-$cr);
//            dump($cpu_remaind);
            $rr=$form->getData()->getRamRemaind();
            $ram_remaind= $rr+ ($ram-$rr);
//            dump($ram_remaind);
//            dump($customer);die();

            $entityManager = $doctrine->getManager();


            $customer->setCpu($cpu)->setRam($ram)->setCpuRemaind($cpu_remaind)->setRamRemaind($ram_remaind);
            $entityManager->persist($customer);
            $entityManager->flush();
            return $this->redirect('/all_machine');
        }
        $customer = $doctrine->getRepository(Machine::class)->findBy(['id' => $id]);
//        dump($customer);die();
        return $this->render('machine/edit.html.twig', array(
            'form' => $form->createView(),'data'=>$customer
        ));
    }

}
