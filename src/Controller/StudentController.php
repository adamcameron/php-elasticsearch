<?php

namespace App\Controller;

use App\Entity\Student;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    #[Route('/students/{id}', name: 'student_detail', requirements: ['id' => '\d+'])]
    public function detail(int $id, EntityManagerInterface $em): Response
    {
        $student = $em->getRepository(Student::class)->find($id);

        if (!$student) {
            throw $this->createNotFoundException('Student not found');
        }

        return $this->render('student/detail.html.twig', [
            'student' => $student,
        ]);
    }
}
