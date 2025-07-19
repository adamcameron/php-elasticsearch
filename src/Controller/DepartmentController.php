<?php

namespace App\Controller;

use App\Entity\Department;
use App\Repository\InstitutionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    #[Route('/departments/{id}', name: 'department_detail')]
    public function detail(Department $department): Response
    {
        return $this->render('department/detail.html.twig', [
            'department' => $department,
        ]);
    }
}
