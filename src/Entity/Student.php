<?php

namespace App\Entity;

use App\Enum\StudentStatus;
use App\Repository\StudentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StudentRepository::class)]
class Student extends AbstractSyncableToElasticsearch
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'students')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Department $department = null;

    #[ORM\Column(length: 255)]
    private ?string $fullName = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateOfBirth = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $gender = null;

    #[ORM\Column]
    private ?int $enrolmentYear = null;

    #[ORM\Column(enumType: StudentStatus::class)]
    private ?StudentStatus $status = null;

    /**
     * @var Collection<int, Enrolment>
     */
    #[ORM\OneToMany(targetEntity: Enrolment::class, mappedBy: 'student')]
    private Collection $enrolments;

    public function __construct()
    {
        $this->enrolments = new ArrayCollection();
    }

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

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): static
    {
        $this->fullName = $fullName;

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

    public function getDateOfBirth(): ?\DateTime
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(\DateTime $dateOfBirth): static
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    public function getEnrolmentYear(): ?int
    {
        return $this->enrolmentYear;
    }

    public function setEnrolmentYear(int $enrolmentYear): static
    {
        $this->enrolmentYear = $enrolmentYear;

        return $this;
    }

    public function getStatus(): ?StudentStatus
    {
        return $this->status;
    }

    public function setStatus(StudentStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, Enrolment>
     */
    public function getEnrolments(): Collection
    {
        return $this->enrolments;
    }

    public function addEnrolment(Enrolment $enrolment): static
    {
        if (!$this->enrolments->contains($enrolment)) {
            $this->enrolments->add($enrolment);
            $enrolment->setStudent($this);
        }

        return $this;
    }

    public function removeEnrolment(Enrolment $enrolment): static
    {
        if ($this->enrolments->removeElement($enrolment)) {
            // set the owning side to null (unless already changed)
            if ($enrolment->getStudent() === $this) {
                $enrolment->setStudent(null);
            }
        }

        return $this;
    }

    public function toElasticsearchArray(): array
    {
        return [
            'email' => $this->email,
            'fullName' => $this->fullName,
            'dateOfBirth' => $this->dateOfBirth?->format('Y-m-d'),
            'gender' => $this->gender,
            'enrolmentYear' => $this->enrolmentYear,
            'status' => $this->status?->label(),
        ];
    }

    public function getSearchTitle(): string
    {
        return $this->fullName;
    }
}
