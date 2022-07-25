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
        $machines = $doctrine->getRepository(Machine::class)->findAll();

        $res = [];
        foreach ($machines as $machine) {
            $processes_count = $machine->getProcesses()->count();
            $machine_cpu_remaind = $machine->getCpuRemaind();
            $machine_ram_remaind = $machine->getRamRemaind();
            $machine_cpu = $machine->getCpu();
            $machine_ram = $machine->getRam();
            $procent_ram = $machine_ram_remaind / $machine_ram * 100;
            $procent_cpu = $machine_cpu_remaind / $machine_cpu * 100;
            $procent_ram = (int)$procent_ram;
            $procent_cpu = (int)$procent_cpu;
            $res[$machine->getId()] = [$processes_count, $procent_cpu, $procent_ram];
        }
        return $this->render('balancer/index.html.twig', ['controller_name' => 'BalancerController',
            'data' => $res]);
    }

    #[Route('/balance', name: 'balance')]
    public function balance(ManagerRegistry $doctrine): Response
    {
        $pocesses = $doctrine->getRepository(Process::class)->findAll();
        $machines_after_balance = [];
        $machines = $doctrine->getRepository(Machine::class)->findAll();

        //set default
        foreach ($machines as $machine) {
            $entityManager = $doctrine->getManager();
            $machine->setCpuRemaind($machine->getCpu());
            $machine->setCpuRemaind($machine->getCpu());
            $machine->setRamRemaind($machine->getRam());
            $entityManager->persist($machine);
            $entityManager->flush();
        }

        //find process
        foreach ($pocesses as $process) {
            $process_cpu = $process->getCpuNeed();
            $process_ram = $process->getRamNeed();

            $machines = $doctrine->getRepository(Machine::class)->createQueryBuilder('machine')
                ->andWhere("machine.cpu_remaind >=  $process_cpu")->andWhere("machine.ram_remaind >=  $process_ram ")
                ->orderBy('machine.cpu_remaind', 'DESC')->setMaxResults(1)
                ->getQuery()->execute();

            foreach ($machines as $machine) {
//            if find machine?
                if (isset($machine)) {
                    $machine_ram_remaind = $machine->getRamRemaind();
                    $machine_cpu_remaind = $machine->getCpuRemaind();

//machine[0]-good machine for process
                    $entityManager = $doctrine->getManager();
                    $machine->setRamRemaind($machine_ram_remaind - $process_ram)->
                    setCpuRemaind($machine_cpu_remaind - $process_cpu)->
                    addProcess($process);
                    $entityManager->persist($machine);
                    $entityManager->flush();

                    $processes_count = $machine->getProcesses()->count();
                    $machine_cpu_remaind = $machine->getCpuRemaind();
                    $machine_ram_remaind = $machine->getRamRemaind();
                    $machine_cpu = $machine->getCpu();
                    $machine_ram = $machine->getRam();
                    $procent_ram = $machine_ram_remaind / $machine_ram * 100;
                    $procent_cpu = $machine_cpu_remaind / $machine_cpu * 100;
                    $procent_ram = (int)$procent_ram;
                    $procent_cpu = (int)$procent_cpu;

                    $machines_after_balance[$machine->getId()] = [$processes_count, $procent_cpu, $procent_ram];

                } else {
                    continue;
                }
            }
        }

        $changed_machines_id = [];
        foreach ($machines_after_balance as $key => $value) {
            $changed_machines_id[] = $key;
        }

        $all_machines_id = [];
        $machines = $doctrine->getRepository(Machine::class)->findAll();
        foreach ($machines as $key => $value) {
            $all_machines_id[] = $value->getId();
        }

        foreach ($all_machines_id as $item) {
            if (!in_array($item, $changed_machines_id)) {
                $not_changed_machines_id[] = $item;
            }
        }

        $not_changed_machines = [];
        foreach ($not_changed_machines_id as $i) {
            $machines_empty = $doctrine->getRepository(Machine::class)->createQueryBuilder('machine')
                ->Where("machine.id =  $i")
                ->getQuery()->execute();
            $not_changed_machines[] = $machines_empty;
        }

        $machines_after_balance_without_changes=[];
        foreach ($not_changed_machines as $machine){
            $processes_count='changes';
            $machine_cpu_remaind = $machine[0]->getCpuRemaind();
            $machine_ram_remaind = $machine[0]->getRamRemaind();
            $machine_cpu = $machine[0]->getCpu();
            $machine_ram = $machine[0]->getRam();
            $procent_ram = $machine_ram_remaind / $machine_ram * 100;
            $procent_cpu = $machine_cpu_remaind / $machine_cpu * 100;
            $procent_ram = (int)$procent_ram;
            $procent_cpu = (int)$procent_cpu;
            $machines_after_balance_without_changes[$machine[0]->getId()] = [$processes_count, $procent_cpu, $procent_ram];
        }

        return $this->render('balancer/balance.html.twig', [
            'controller_name' => 'MachineController',
            'data' => $machines_after_balance,
            'not_changed_data' => $machines_after_balance_without_changes,
            'error' => "SORRY you can't delete a machine, as only it can run process ",
        ]);
    }
}

