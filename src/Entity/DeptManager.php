<?php

namespace App\Entity;

use App\Repository\DeptManagerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: DeptManagerRepository::class)]
class DeptManager
{

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'deptManagers')]
    #[ORM\JoinColumn(name: 'emp_no', referencedColumnName: 'emp_no', nullable: false)]
    private ?Employee $employee = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'deptManagers')]
    #[ORM\JoinColumn(name: 'dept_no', referencedColumnName: 'dept_no', nullable: false)]
    private ?Department $department = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $fromDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $toDate = null;

    // public function getId(): ?int
    // {
    //     return $this->id;
    // }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function setEmployee(?Employee $employee): static
    {
        $this->employee = $employee;


        return $this;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(?Department $department): static
    {
        $this->department = $department;

        return $this;
    }

    public function getFromDate(): ?\DateTimeInterface
    {
        return $this->fromDate;
    }

    public function setFromDate(\DateTimeInterface $fromDate): static
    {
        $this->fromDate = $fromDate;

        return $this;
    }

    public function getToDate(): ?\DateTimeInterface
    {
        return $this->toDate;
    }

    public function setToDate(\DateTimeInterface $toDate): static
    {
        $this->toDate = $toDate;

        return $this;
    }
}
