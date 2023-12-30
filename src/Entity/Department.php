<?php

namespace App\Entity;

use App\Repository\DepartmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table('departments')]
#[ORM\Entity(repositoryClass: DepartmentRepository::class)]
class Department
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\Column(name:'dept_no')]
    private ?string $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $roi = null;

    #[ORM\Column(length: 40)]
    private ?string $deptName = null;

    #[ORM\OneToMany(mappedBy: 'department', targetEntity: DeptTitle::class)]
    private Collection $deptTitles;

    #[ORM\OneToOne(mappedBy: 'departement', cascade: ['persist', 'remove'])]
    private ?DeptManager $deptManager = null;

    public function __construct()
    {
        $this->deptTitles = new ArrayCollection();
    }


    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getRoi(): ?string
    {
        return $this->roi;
    }

    public function setRoi(?string $roi): static
    {
        $this->roi = $roi;

        return $this;
    }

    public function getDeptName(): ?string
    {
        return $this->deptName;
    }

    public function setDeptName(string $deptName): static
    {
        $this->deptName = $deptName;

        return $this;
    }

    /**
     * @return Collection<int, DeptTitle>
     */
    public function getDeptTitles(): Collection
    {
        return $this->deptTitles;
    }

    public function addDeptTitle(DeptTitle $deptTitle): static
    {
        if (!$this->deptTitles->contains($deptTitle)) {
            $this->deptTitles->add($deptTitle);
            $deptTitle->setDepartment($this);
        }

        return $this;
    }

    public function removeDeptTitle(DeptTitle $deptTitle): static
    {
        if ($this->deptTitles->removeElement($deptTitle)) {
            // set the owning side to null (unless already changed)
            if ($deptTitle->getDepartment() === $this) {
                $deptTitle->setDepartment(null);
            }
        }

        return $this;
    }

    public function getDeptManager(): ?DeptManager
    {
        return $this->deptManager;
    }

    public function setDeptManager(DeptManager $deptManager): static
    {
        // set the owning side of the relation if necessary
        if ($deptManager->getDepartement() !== $this) {
            $deptManager->setDepartement($this);
        }

        $this->deptManager = $deptManager;

        return $this;
    }

    
}
