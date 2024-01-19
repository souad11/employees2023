<?php

namespace App\Entity;

use App\Repository\ProjetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjetRepository::class)]
#[ORM\Table(name: 'projects')]
class Projet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $modified = null;
    
    #[ORM\OneToMany(mappedBy: 'projet', targetEntity: EmpProjet::class)]
    #[ORM\JoinColumn(name: 'project_id', referencedColumnName: 'id')]
    private Collection $empProjets;

    #[ORM\ManyToOne(inversedBy: 'projets')]
    #[ORM\JoinColumn(name: 'emp_no', referencedColumnName: 'emp_no', nullable: false)]
    private ?Employee $employee = null;

    public function __construct()
    {
        $this->empProjets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): static
    {
        $this->created = $created;

        return $this;
    }

    public function getModified(): ?\DateTimeInterface
    {
        return $this->modified;
    }

    public function setModified(?\DateTimeInterface $modified): static
    {
        $this->modified = $modified;

        return $this;
    }

    /**
     * @return Collection<int, EmpProjet>
     */
    public function getEmpProjets(): Collection
    {
        return $this->empProjets;
    }

    public function addEmpProjet(EmpProjet $empProjet): static
    {
        if (!$this->empProjets->contains($empProjet)) {
            $this->empProjets->add($empProjet);
            $empProjet->setProjet($this);
        }

        return $this;
    }

    public function removeEmpProjet(EmpProjet $empProjet): static
    {
        if ($this->empProjets->removeElement($empProjet)) {
            // set the owning side to null (unless already changed)
            if ($empProjet->getProjet() === $this) {
                $empProjet->setProjet(null);
            }
        }

        return $this;
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function setEmployee(?Employee $employee): static
    {
        $this->employee = $employee;

        return $this;
    }
}
