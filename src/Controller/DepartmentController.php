<?php

namespace App\Controller;

use App\Entity\Department;
use App\Form\DepartmentType;
use App\Repository\InstitutionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DepartmentController extends AbstractController
{
    #[Route('/departments', name: 'department_list')]
    public function list(InstitutionRepository $institutionRepository): Response
    {
        $institutions = $institutionRepository->findBy([], ['name' => 'ASC']);

        return $this->render('department/list.html.twig', [
            'institutions' => $institutions,
        ]);
    }

    #[Route('/departments/{id}/view', name: 'department_view')]
    public function view(Department $department): Response
    {
        return $this->render('department/view.html.twig', [
            'department' => $department,
        ]);
    }

    #[Route('/departments/{id}/edit', name: 'department_edit', requirements: ['id' => '\d+'])]
    public function edit(Department $department, EntityManagerInterface $em, Request $request): Response
    {
        $form = $this->createForm(DepartmentType::class, $department);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('department_view', ['id' => $department->getId()]);
        }

        return $this->render('department/edit.html.twig', [
            'department' => $department,
            'form' => $form->createView(),
        ]);
    }

}
