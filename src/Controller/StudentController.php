<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Department;
use App\Entity\Enrolment;
use App\Entity\Student;
use App\Event\StudentRequestEvent;
use App\Form\StudentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class StudentController extends AbstractController
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher
    ) {

    }


    #[Route('/students', name: 'student_list')]
    public function list(EntityManagerInterface $em): Response
    {
        $qb = $em->createQueryBuilder();
        $qb->select('i', 'd', 's')
            ->from('App\Entity\Institution', 'i')
            ->join('i.departments', 'd')
            ->join('d.students', 's')
            ->groupBy('i.id, d.id, s.id')
            ->orderBy('i.name', 'ASC')
            ->addOrderBy('d.name', 'ASC')
            ->addOrderBy('s.fullName', 'ASC');

        $institutions = $qb->getQuery()->getResult();

        return $this->render('student/list.html.twig', [
            'institutions' => $institutions,
        ]);
    }

    #[Route('/students/{id}/view', name: 'student_view', requirements: ['id' => '\d+'])]
    public function view(Student $student, EntityManagerInterface $em): Response
    {
        return $this->render('student/view.html.twig', [
            'student' => $student,
        ]);
    }

    #[Route('/students/{id}/edit', name: 'student_edit', requirements: ['id' => '\d+'])]
    public function edit(Student $student, EntityManagerInterface $em, Request $request): Response
    {
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->eventDispatcher->dispatch(new StudentRequestEvent($request, $student));

            return $this->redirectToRoute('student_view', ['id' => $student->getId()]);
        }

        return $this->render('student/edit.html.twig', [
            'student' => $student,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/courses/{id}/students/add', name: 'student_add')]
    public function add(
        Course $course,
        EntityManagerInterface $em,
        Request $request
    ): Response {
        $student = new Student();
        $student->setDepartment($course->getDepartment());

        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($student);

            $enrolment = new Enrolment();
            $enrolment->setStudent($student);
            $enrolment->setCourse($course);
            $em->persist($enrolment);

            $em->flush();

            $this->eventDispatcher->dispatch(new StudentRequestEvent($request, $student));

            return $this->redirectToRoute('course_view', ['id' => $course->getId()]);
        }

        return $this->render('student/add.html.twig', [
            'form' => $form->createView(),
            'course' => $course,
        ]);
    }
    #[Route('/students/{id}/delete', name: 'student_delete', requirements: ['id' => '\d+'])]
    public function delete(Student $student, EntityManagerInterface $em, Request $request): Response
    {
        foreach ($student->getEnrolments() as $enrolment) {
            $this->eventDispatcher->dispatch(new StudentRequestEvent($request, $student));
            $em->remove($enrolment);
        }

        $em->remove($student);
        $em->flush();

        return $this->redirectToRoute('student_list');
    }
}
