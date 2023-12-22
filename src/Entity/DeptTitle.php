<?php

namespace App\Entity;

use App\Repository\DeptTitleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table('dept_title')]
#[ORM\Entity(repositoryClass: DeptTitleRepository::class)]
class DeptTitle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'deptTitles')]
    #[ORM\JoinColumn(name: 'dept_no', referencedColumnName: 'dept_no', nullable: false)]
    private ?Department $department = null;

    #[ORM\ManyToOne(inversedBy: 'deptTitles')]
    #[ORM\JoinColumn(name: 'title_id', referencedColumnName: 'id', nullable: false)]
    private ?Title $title = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTitle(): ?Title
    {
        return $this->title;
    }

    public function setTitle(?Title $title): static
    {
        $this->title = $title;

        return $this;
    }

}
