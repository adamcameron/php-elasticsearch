<?php

namespace App\Controller;

use App\Entity\Institution;
use App\Entity\Instructor;
use App\Form\InstructorType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InstructorController extends AbstractController
{
    #[Route('/instructors', name: 'instructor_list')]
    public function list(EntityManagerInterface $em): Response
    {
        $institutions = $em
            ->getRepository(Institution::class)
            ->createQueryBuilder('i')
            ->leftJoin('i.departments', 'd')
            ->addSelect('d')
            ->leftJoin('d.instructors', 'ins')
            ->addSelect('ins')
            ->orderBy('i.name', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('instructor/list.html.twig', [
            'institutions' => $institutions,
        ]);
    }

    #[Route('/instructors/{id}/view', name: 'instructor_view', requirements: ['id' => '\d+'])]
    public function view(Instructor $instructor, EntityManagerInterface $em): Response
    {
        return $this->render('instructor/view.html.twig', [
            'instructor' => $instructor,
        ]);
    }

    #[Route('/instructors/{id}/edit', name: 'instructor_edit', requirements: ['id' => '\d+'])]
    public function edit(Instructor $instructor, EntityManagerInterface $em, Request $request): Response
    {
        $form = $this->createForm(InstructorType::class, $instructor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('instructor_view', ['id' => $instructor->getId()]);
        }

        return $this->render('instructor/edit.html.twig', [
            'instructor' => $instructor,
            'form' => $form->createView(),
        ]);
    }
}
