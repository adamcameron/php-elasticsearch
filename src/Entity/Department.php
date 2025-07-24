<?php

namespace App\Entity;

use App\Repository\DepartmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DepartmentRepository::class)]
class Department extends AbstractSyncableToElasticsearch
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'departments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Institution $institution = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $headOfDepartment = null;

    #[ORM\Column(length: 255)]
    private ?string $building = null;

    #[ORM\Column(length: 255)]
    private ?string $contactEmail = null;

    /**
     * @var Collection<int, Student>
     */
    #[ORM\OneToMany(targetEntity: Student::class, mappedBy: 'department')]
    #[ORM\OrderBy(['fullName' => 'ASC'])]
    private Collection $students;

    /**
     * @var Collection<int, Course>
     */
    #[ORM\OneToMany(targetEntity: Course::class, mappedBy: 'department')]
    #[ORM\OrderBy(['title' => 'ASC'])]
    private Collection $courses;

    /**
     * @var Collection<int, Instructor>
     */
    #[ORM\OneToMany(targetEntity: Instructor::class, mappedBy: 'department')]
    #[ORM\OrderBy(['fullName' => 'ASC'])]
    private Collection $instructors;

    public function __construct()
    {
        $this->students = new ArrayCollection();
        $this->courses = new ArrayCollection();
        $this->instructors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInstitution(): ?Institution
    {
        return $this->institution;
    }

    public function setInstitution(?Institution $institution): static
    {
        $this->institution = $institution;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getHeadOfDepartment(): ?string
    {
        return $this->headOfDepartment;
    }

    public function setHeadOfDepartment(string $headOfDepartment): static
    {
        $this->headOfDepartment = $headOfDepartment;

        return $this;
    }

    public function getBuilding(): ?string
    {
        return $this->building;
    }

    public function setBuilding(string $building): static
    {
        $this->building = $building;

        return $this;
    }

    public function getContactEmail(): ?string
    {
        return $this->contactEmail;
    }

    public function setContactEmail(string $contactEmail): static
    {
        $this->contactEmail = $contactEmail;

        return $this;
    }

    /**
     * @return Collection<int, Student>
     */
    public function getStudents(): Collection
    {
        return $this->students;
    }

    public function addStudent(Student $student): static
    {
        if (!$this->students->contains($student)) {
            $this->students->add($student);
            $student->setDepartment($this);
        }

        return $this;
    }

    public function removeStudent(Student $student): static
    {
        if ($this->students->removeElement($student)) {
            // set the owning side to null (unless already changed)
            if ($student->getDepartment() === $this) {
                $student->setDepartment(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Course>
     */
    public function getCourses(): Collection
    {
        return $this->courses;
    }

    public function addCourse(Course $course): static
    {
        if (!$this->courses->contains($course)) {
            $this->courses->add($course);
            $course->setDepartment($this);
        }

        return $this;
    }

    public function removeCourse(Course $course): static
    {
        if ($this->courses->removeElement($course)) {
            // set the owning side to null (unless already changed)
            if ($course->getDepartment() === $this) {
                $course->setDepartment(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Instructor>
     */
    public function getInstructors(): Collection
    {
        return $this->instructors;
    }

    public function addInstructor(Instructor $instructor): static
    {
        if (!$this->instructors->contains($instructor)) {
            $this->instructors->add($instructor);
            $instructor->setDepartment($this);
        }

        return $this;
    }

    public function removeInstructor(Instructor $instructor): static
    {
        if ($this->instructors->removeElement($instructor)) {
            // set the owning side to null (unless already changed)
            if ($instructor->getDepartment() === $this) {
                $instructor->setDepartment(null);
            }
        }

        return $this;
    }

    public function toElasticsearchArray(): array
    {
        return [
            'name' => $this->name,
            'headOfDepartment' => $this->headOfDepartment,
            'building' => $this->building,
            'contactEmail' => $this->contactEmail,
        ];
    }

    public function getSearchTitle(): string
    {
        return $this->name;
    }
}
