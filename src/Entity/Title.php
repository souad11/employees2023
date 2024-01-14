<?php

namespace App\Entity;

use App\Repository\TitleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table('titles')]
#[ORM\Entity(repositoryClass: TitleRepository::class)]
class Title
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'title', targetEntity: DeptTitle::class)]
    private Collection $deptTitles;

    #[ORM\OneToMany(mappedBy: 'title', targetEntity: EmpTitle::class)]
    private Collection $empTitles;

    public function __construct()
    {
        $this->deptTitles = new ArrayCollection();
        $this->empTitles = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

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
            $deptTitle->setTitle($this);
        }

        return $this;
    }

    public function removeDeptTitle(DeptTitle $deptTitle): static
    {
        if ($this->deptTitles->removeElement($deptTitle)) {
            // set the owning side to null (unless already changed)
            if ($deptTitle->getTitle() === $this) {
                $deptTitle->setTitle(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, EmpTitle>
     */
    public function getEmpTitles(): Collection
    {
        return $this->empTitles;
    }

    public function addEmpTitle(EmpTitle $empTitle): static
    {
        if (!$this->empTitles->contains($empTitle)) {
            $this->empTitles->add($empTitle);
            $empTitle->setTitle($this);
        }

        return $this;
    }

    public function removeEmpTitle(EmpTitle $empTitle): static
    {
        if ($this->empTitles->removeElement($empTitle)) {
            // set the owning side to null (unless already changed)
            if ($empTitle->getTitle() === $this) {
                $empTitle->setTitle(null);
            }
        }

        return $this;
    }

}
