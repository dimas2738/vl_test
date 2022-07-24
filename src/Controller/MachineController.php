<?php

namespace App\Controller;

use App\Entity\Machine;

use App\Entity\Process;
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
        //find machine by id
        $em = $doctrine->getManager();
        $machine_to_del = $doctrine->getRepository(Machine::class)->findBy(['id' => $id]);
        $machine_to_del_ram=$machine_to_del[0]->getRam();
        $machine_to_del_cpu=$machine_to_del[0]->getCpu();

//        get processes from machine
        $processes_to_del = $machine_to_del[0]->getProcesses();
//        dump($processes_to_del);die();

        // if machine have process
        if (count($processes_to_del)>=1){

            $del=[];

            foreach ($processes_to_del as $process_to_del) {
                $process_to_del_cpu_need = $process_to_del->getCpuNeed();
                $process_to_del_ram_need = $process_to_del->getRamNeed();

                //find from all machines good for processes from del_machine

                $machine = $doctrine->getRepository(Machine::class)->createQueryBuilder('machine')
                    ->andWhere("machine.cpu_remaind >= $process_to_del_cpu_need")->andWhere("machine.ram_remaind >=  $process_to_del_ram_need")
                    ->orderBy('machine.cpu_remaind', 'DESC')->setMaxResults(1)
                    ->getQuery()->execute();

//            if find machine?
                if (count($machine)>=1){
                    $machine_ram_remaind=$machine[0]->getRamRemaind();
                    $machine_cpu_remaind=$machine[0]->getCpuRemaind();


//machine[0]-good machine for process
                    $entityManager=$doctrine->getManager();
                    $machine[0]->
                    setRamRemaind( $machine_ram_remaind - $process_to_del_ram_need)->
                    setCpuRemaind($machine_cpu_remaind -  $process_to_del_cpu_need)->addProcess($process_to_del);
                    $entityManager->persist($machine[0]);
                    $entityManager->flush();
//machine_to_del[0]-past machine for process
                    $entityManager=$doctrine->getManager();
                    $machine_to_del[0] ->removeProcess($process_to_del);
//->setCpuRemaind($machine_to_del_cpu+ $process_to_del_cpu_need)->
//                    setRamRemaind($machine_to_del_ram+ $process_to_del_ram_need);

                    $entityManager->persist( $machine_to_del[0]);
                    $entityManager->flush();
////                    $entityManager->remove($machine_to_del[0]);
////                    $entityManager->flush();
//
//                    return $this->redirect('/all_machine');

                }

                //if not find machine
                else {
//                    $machine[0]_
                    return $this->render('machine/show.html.twig', [
                        'controller_name' => 'MachineController',
                        'error' => "SORRY you can't delete a machine, as only it can run process no.",
                    ]);
                }
            }


        }
//
        $entityManager=$doctrine->getManager();
        $entityManager->remove($machine_to_del[0]);
        $entityManager->flush();

        return $this->redirect('/all_machine');
    }



    #[Route('/edit_machine/{id}', name: 'edit_machine')]
    public function edit_machine(Request $request, ManagerRegistry $doctrine, $id): Response
    {
        $machine = $doctrine->getRepository(Machine::class)->findOneBy(['id' => $id]);

        $ram_machine = $machine->getRam();
        $cpu_machine = $machine->getCpu();
//        dump($ram_machine,$ram_machine);
        $ram_remaind = $machine->getRamRemaind();
        $cpu_remaind = $machine->getCpuRemaind();

        $form = $this->createForm(AddMachineFormType::class, $machine);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            //get data from form
            $ram_form = $form->getData()->getRam();
            $cpu_form = $form->getData()->getCpu();
            //get data from machine

            //get fact
//            $ram_machine = $machine->getRam();
//            $cpu_machine = $machine->getCpu();


//            //get remaind
//            $ram_remaind = $machine->getRamRemaind();
//            $cpu_remaind = $machine->getCpuRemaind();

            //make new data
            $new_cpu_remaind = $cpu_form - $cpu_machine + $cpu_remaind;
            $new_ram_remaind = $ram_form - $ram_machine + $ram_remaind;
//            dump($new_cpu_remaind,$new_ram_remaind);die();


            //execute
            $entityManager = $doctrine->getManager();
            $machine->setCpu($cpu_form)->setRam($ram_form)->setCpuRemaind($new_cpu_remaind)->setRamRemaind($new_ram_remaind);
            $entityManager->persist($machine);
            $entityManager->flush();
            return $this->redirect('/all_machine');
        }
        $customer = $doctrine->getRepository(Machine::class)->findBy(['id' => $id]);
        return $this->render('machine/edit.html.twig', array(
            'form' => $form->createView(), 'data' => $customer
        ));
    }

}
