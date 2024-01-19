<?php

namespace App\Entity;

use App\Repository\EmployeeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;


enum Gender: string {
    case Homme='M';
    case Femme='F';
    case Non_binary='X';
}

#[ORM\Table('employees')]
#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
class Employee implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\Column(name: 'emp_no')]

    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $birthDate = null;

    #[ORM\Column(length: 14)]
    #[Assert\Length(min: 3, max: 14)]
    private ?string $firstName = null;

    #[ORM\Column(length: 16)]
    #[Assert\Length(min: 3, max: 16)]
    private ?string $lastName = null;

    #[ORM\Column(length: 1, type: 'string' , enumType: Gender::class)]
    
    private ?Gender $gender = null;
    

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $hireDate = null;

    #[ORM\Column(length: 255, nullable: true)]

    private ?string $photo = null;


    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'employe', targetEntity: Demand::class)]
    private Collection $demands;

    #[ORM\OneToMany(mappedBy: 'employee', targetEntity: EmpTitle::class)]
    private Collection $empTitles;

    #[ORM\OneToOne(mappedBy: 'employee', cascade: ['persist', 'remove'])]
    private ?DeptManager $deptManager = null;

    #[ORM\OneToMany(mappedBy: 'employee', targetEntity: DeptEmp::class, cascade: ['persist', 'remove'])] 
    private Collection $deptEmps;

    #[ORM\OneToMany(mappedBy: 'employee', targetEntity: Salary::class)]
    private Collection $salaries;

    #[ORM\ManyToMany(targetEntity: Mission::class, mappedBy: 'employee')]
    private Collection $missions;

    #[ORM\OneToMany(mappedBy: 'employee', targetEntity: EmpProjet::class)]
    private Collection $empProjets;

    #[ORM\ManyToOne(inversedBy: 'employees')]
    #[ORM\OneToMany(mappedBy: 'employee', targetEntity: Projet::class)]
    private Collection $projets;


    public function __construct()
    {
        $this->demands = new ArrayCollection();
        $this->empTitles = new ArrayCollection();
        $this->deptEmps = new ArrayCollection();
        $this->salaries = new ArrayCollection();
        $this->roles[] = 'ROLE_USER';
        $this->missions = new ArrayCollection();
        $this->empProjets = new ArrayCollection();
        $this->projets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTimeInterface $birthDate): static
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getGender(): ?Gender
    {
        return $this->gender;
    }

    public function setGender(Gender $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    public function getHireDate(): ?\DateTimeInterface
    {
        return $this->hireDate;
    }

    public function setHireDate(\DateTimeInterface $hireDate): static
    {
        $this->hireDate = $hireDate;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function __toString(): string
    {
        return "{$this->firstName} {$this->lastName}";
    }

    /**
     * @return Collection<int, Demand>
     */
    public function getDemands(): Collection
    {
        return $this->demands;
    }

    public function addDemand(Demand $demand): static
    {
        if (!$this->demands->contains($demand)) {
            $this->demands->add($demand);
            $demand->setEmploye($this);
        }

        return $this;
    }

    public function removeDemand(Demand $demand): static
    {
        if ($this->demands->removeElement($demand)) {
            // set the owning side to null (unless already changed)
            if ($demand->getEmploye() === $this) {
                $demand->setEmploye(null);
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
            $empTitle->setEmployee($this);
        }

        return $this;
    }

    public function removeEmpTitle(EmpTitle $empTitle): static
    {
        if ($this->empTitles->removeElement($empTitle)) {
            // set the owning side to null (unless already changed)
            if ($empTitle->getEmployee() === $this) {
                $empTitle->setEmployee(null);
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
        if ($deptManager->getEmployee() !== $this) {
            $deptManager->setEmployee($this);
        }

        $this->deptManager = $deptManager;

        return $this;
    }

    /**
     * @return Collection<int, DeptEmp>
     */
    public function getDeptEmps(): Collection
    {
        return $this->deptEmps;
    }

    public function addDeptEmp(DeptEmp $deptEmp): static
    {
        if (!$this->deptEmps->contains($deptEmp)) {
            $this->deptEmps->add($deptEmp);
            $deptEmp->setEmployee($this);
        }

        return $this;
    }

    public function removeDeptEmp(DeptEmp $deptEmp): static
    {
        if ($this->deptEmps->removeElement($deptEmp)) {
            // set the owning side to null (unless already changed)
            if ($deptEmp->getEmployee() === $this) {
                $deptEmp->setEmployee(null);
            }
        }

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        // guarantee every user at least has ROLE_USER
        
        $roles[] = 'ROLE_USER';
        

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isAdmin(): bool
    {
        return in_array('ROLE_ADMIN', $this->roles);
    }

    /**
     * @return Collection<int, Salary>
     */
    public function getSalaries(): Collection
    {
        return $this->salaries;
    }

    public function addSalary(Salary $salary): static
    {
        if (!$this->salaries->contains($salary)) {
            $this->salaries->add($salary);
            $salary->setEmployee($this);
        }

        return $this;
    }

    public function removeSalary(Salary $salary): static
    {
        if ($this->salaries->removeElement($salary)) {
            // set the owning side to null (unless already changed)
            if ($salary->getEmployee() === $this) {
                $salary->setEmployee(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Mission>
     */
    public function getMissions(): Collection
    {
        return $this->missions;
    }

    public function addMission(Mission $mission): static
    {
        if (!$this->missions->contains($mission)) {
            $this->missions->add($mission);
            $mission->addEmployee($this);
        }

        return $this;
    }

    public function removeMission(Mission $mission): static
    {
        if ($this->missions->removeElement($mission)) {
            $mission->removeEmployee($this);
        }

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
            $empProjet->setEmployee($this);
        }

        return $this;
    }

    public function removeEmpProjet(EmpProjet $empProjet): static
    {
        if ($this->empProjets->removeElement($empProjet)) {
            // set the owning side to null (unless already changed)
            if ($empProjet->getEmployee() === $this) {
                $empProjet->setEmployee(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Projet>
     */
    public function getProjets(): Collection
    {
        return $this->projets;
    }

    public function addProjet(Projet $projet): static
    {
        if (!$this->projets->contains($projet)) {
            $this->projets->add($projet);
            $projet->setEmployee($this);
        }

        return $this;
    }

    public function removeProjet(Projet $projet): static
    {
        if ($this->projets->removeElement($projet)) {
            // set the owning side to null (unless already changed)
            if ($projet->getEmployee() === $this) {
                $projet->setEmployee(null);
            }
        }

        return $this;
    }

}
