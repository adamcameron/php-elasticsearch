<?php

namespace App\Controller;

use App\Entity\Institution;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InstitutionController extends AbstractController
{
    #[Route('/institutions', name: 'institution_list')]
    public function list(EntityManagerInterface $em): Response
    {
        $institutions = $em
            ->getRepository(Institution::class)->findBy([], ['name' => 'ASC']);

        return $this->render('institution/list.html.twig', [
            'institutions' => $institutions,
        ]);
    }

    #[Route('/institutions/{id}', name: 'institution_detail', requirements: ['id' => '\d+'])]
    public function detail(int $id, EntityManagerInterface $em): Response
    {
        $institution = $em->getRepository(Institution::class)->find($id);

        if (!$institution) {
            throw $this->createNotFoundException('Institution not found');
        }

        return $this->render('institution/detail.html.twig', [
            'institution' => $institution,
        ]);
    }
}
