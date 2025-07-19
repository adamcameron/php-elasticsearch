<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Institution;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CourseController extends AbstractController
{
    #[Route('/courses', name: 'course_list')]
    public function list(EntityManagerInterface $em): Response
    {
        $institutions = $em->getRepository(Institution::class)
            ->createQueryBuilder('i')
            ->leftJoin('i.departments', 'd')
            ->addSelect('d')
            ->leftJoin('d.courses', 'c')
            ->addSelect('c')
            ->orderBy('i.name', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('course/list.html.twig', [
            'institutions' => $institutions,
        ]);
    }

    #[Route('/courses/{id}', name: 'course_detail', requirements: ['id' => '\d+'])]
    public function detail(int $id, EntityManagerInterface $em): Response
    {
        $course = $em->getRepository(Course::class)->find($id);

        if (!$course) {
            throw $this->createNotFoundException('Course not found');
        }

        return $this->render('course/detail.html.twig', [
            'course' => $course,
        ]);
    }
}
