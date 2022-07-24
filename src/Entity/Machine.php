<?php

namespace App\Entity;

use App\Repository\MachineRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MachineRepository::class)]
class Machine
{


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $cpu = null;

    #[ORM\Column]
    private ?int $ram = null;

    #[ORM\Column]
    private ?int $cpu_remaind = null;

    #[ORM\Column]
    private ?int $ram_remaind = null;

    #[ORM\OneToMany(mappedBy: 'machine', targetEntity: Process::class)]
    private Collection $processes;

    public function __construct()
    {
        $this->processes = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCpu(): ?int
    {
        return $this->cpu;
    }

    public function setCpu(int $cpu): self
    {
        $this->cpu = $cpu;

        return $this;
    }

    public function getRam(): ?int
    {
        return $this->ram;
    }

    public function setRam(int $ram): self
    {
        $this->ram = $ram;

        return $this;
    }

    public function getCpuRemaind(): ?int
    {
        return $this->cpu_remaind;
    }

    public function setCpuRemaind(int $cpu_remaind): self
    {
        $this->cpu_remaind = $cpu_remaind;

        return $this;
    }

    public function getRamRemaind(): ?int
    {
        return $this->ram_remaind;
    }

    public function setRamRemaind(int $ram_remaind): self
    {
        $this->ram_remaind = $ram_remaind;

        return $this;
    }



//

/**
 * @return Collection<int, Process>
 */
public function getProcesses(): Collection
{
    return $this->processes;
}

public function addProcess(Process $process): self
{
    if (!$this->processes->contains($process)) {
        $this->processes[] = $process;
        $process->setMachine($this);
    }

    return $this;
}

public function removeProcess(Process $process): self
{
    if ($this->processes->removeElement($process)) {
        // set the owning side to null (unless already changed)
        if ($process->getMachine() === $this) {
            $process->setMachine(null);
        }
    }

    return $this;
}



}
