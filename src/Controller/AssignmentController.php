<?php

namespace App\Controller;

use App\Entity\Assignment;
use App\Entity\Institution;
use App\Form\AssignmentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AssignmentController extends AbstractController
{
    #[Route('/assignments', name: 'assignment_list')]
    public function list(EntityManagerInterface $em): Response
    {
        $institutions = $em
            ->getRepository(Institution::class)
            ->createQueryBuilder('i')
            ->leftJoin('i.departments', 'd')
            ->addSelect('d')
            ->leftJoin('d.courses', 'c')
            ->addSelect('c')
            ->leftJoin('c.assignments', 'a')
            ->addSelect('a')
            ->orderBy('i.name', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('assignment/list.html.twig', [
            'institutions' => $institutions,
        ]);
    }

    #[Route('/assignments/{id}/view', name: 'assignment_view', requirements: ['id' => '\d+'])]
    public function view(Assignment $assignment, EntityManagerInterface $em): Response
    {
        return $this->render('assignment/view.html.twig', [
            'assignment' => $assignment,
        ]);
    }

    #[Route('/assignments/{id}/edit', name: 'assignment_edit', requirements: ['id' => '\d+'])]
    public function edit(Assignment $assignment, EntityManagerInterface $em, Request $request): Response
    {
        $form = $this->createForm(AssignmentType::class, $assignment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('assignment_view', ['id' => $assignment->getId()]);
        }

        return $this->render('assignment/edit.html.twig', [
            'assignment' => $assignment,
            'form' => $form->createView(),
        ]);
    }
}
