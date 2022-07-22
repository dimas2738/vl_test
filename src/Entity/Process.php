<?php

namespace App\Entity;

use App\Repository\ProcessRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProcessRepository::class)]
class Process
{


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $cpu_need = null;

    #[ORM\Column]
    private ?int $ram_need = null;

    #[ORM\ManyToOne(inversedBy: 'processes')]
    private ?Machine $machine = null;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCpuNeed(): ?int
    {
        return $this->cpu_need;
    }

    public function setCpuNeed(int $cpu_need): self
    {
        $this->cpu_need = $cpu_need;

        return $this;
    }

    public function getRamNeed(): ?int
    {
        return $this->ram_need;
    }

    public function setRamNeed(int $ram_need): self
    {
        $this->ram_need = $ram_need;

        return $this;
    }

    public function getMachine(): Machine
    {
        return $this->machine;
    }

    public function setMachine(Machine $machine): self
    {
        $this->machine = $machine->getId();

        return $this;
    }


}
