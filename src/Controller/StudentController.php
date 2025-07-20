<?php

namespace App\Controller;

use App\Entity\Student;
use App\Form\StudentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StudentController extends AbstractController
{
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
    public function detail(int $id, EntityManagerInterface $em): Response
    {
        $student = $em->getRepository(Student::class)->find($id);

        if (!$student) {
            throw $this->createNotFoundException('Student not found');
        }

        return $this->render('student/view.html.twig', [
            'student' => $student,
        ]);
    }

    #[Route('/students/{id}/edit', name: 'student_edit', requirements: ['id' => '\d+'])]
    public function edit(int $id, EntityManagerInterface $em, Request $request): Response
    {
        $student = $em->getRepository(Student::class)->find($id);

        if (!$student) {
            throw $this->createNotFoundException('Student not found');
        }

        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('student_view', ['id' => $student->getId()]);
        }

        return $this->render('student/edit.html.twig', [
            'student' => $student,
            'form' => $form->createView(),
        ]);
    }

}
