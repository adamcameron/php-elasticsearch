<?php

namespace App\Controller;

use App\Entity\Assignment;
use App\Entity\Institution;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AssignmentController extends AbstractController
{
    #[Route('/assignments', name: 'assignment_list')]
    public function list(EntityManagerInterface $em): Response
    {
        $institutions = $em->getRepository(Institution::class)
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

    #[Route('/assignments/{id}', name: 'assignment_detail', requirements: ['id' => '\d+'])]
    public function detail(int $id, EntityManagerInterface $em): Response
    {
        $assignment = $em->getRepository(Assignment::class)->find($id);

        if (!$assignment) {
            throw $this->createNotFoundException('Assignment not found');
        }

        return $this->render('assignment/detail.html.twig', [
            'assignment' => $assignment,
        ]);
    }
}
