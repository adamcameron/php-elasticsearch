<?php

namespace App\Controller;

use App\Entity\Institution;
use App\Entity\Instructor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InstructorController extends AbstractController
{
    #[Route('/instructors', name: 'instructor_list')]
    public function list(EntityManagerInterface $em): Response
    {
        $institutions = $em->getRepository(Institution::class)
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

    #[Route('/instructors/{id}', name: 'instructor_detail', requirements: ['id' => '\d+'])]
    public function detail(int $id, EntityManagerInterface $em): Response
    {
        $instructor = $em->getRepository(Instructor::class)->find($id);

        if (!$instructor) {
            throw $this->createNotFoundException('Instructor not found');
        }

        return $this->render('instructor/detail.html.twig', [
            'instructor' => $instructor,
        ]);
    }
}
