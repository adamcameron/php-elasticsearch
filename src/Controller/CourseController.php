<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Institution;
use App\Form\CourseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/courses/{id}/view', name: 'course_view', requirements: ['id' => '\d+'])]
    public function detail(int $id, EntityManagerInterface $em): Response
    {
        $course = $em->getRepository(Course::class)->find($id);

        if (!$course) {
            throw $this->createNotFoundException('Course not found');
        }

        return $this->render('course/view.html.twig', [
            'course' => $course,
        ]);
    }

    #[Route('/courses/{id}/edit', name: 'course_edit', requirements: ['id' => '\d+'])]
    public function edit(int $id, EntityManagerInterface $em, Request $request): Response
    {
        $course = $em->getRepository(Course::class)->find($id);

        if (!$course) {
            throw $this->createNotFoundException('Course not found');
        }

        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('course_view', ['id' => $course->getId()]);
        }

        return $this->render('course/edit.html.twig', [
            'course' => $course,
            'form' => $form->createView(),
        ]);
    }
}
