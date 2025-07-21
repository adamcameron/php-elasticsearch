<?php

namespace App\Controller;

use App\Entity\Institution;
use App\Form\InstitutionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InstitutionController extends AbstractController
{
    #[Route('/institutions', name: 'institution_list')]
    public function list(EntityManagerInterface $em): Response
    {
        $institutions = $em
            ->getRepository(Institution::class)
            ->findBy([], ['name' => 'ASC']);

        return $this->render('institution/list.html.twig', [
            'institutions' => $institutions,
        ]);
    }

    #[Route('/institutions/{id}/view', name: 'institution_view', requirements: ['id' => '\d+'])]
    public function view(Institution $institution, EntityManagerInterface $em): Response
    {
        return $this->render('institution/view.html.twig', [
            'institution' => $institution,
        ]);
    }

    #[Route('/institutions/{id}/edit', name: 'institution_edit')]
    public function edit(Institution $institution, EntityManagerInterface $em, Request $request): Response {
        $form = $this->createForm(InstitutionType::class, $institution);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('institution_view', ['id' => $institution->getId()]);
        }

        return $this->render('institution/edit.html.twig', [
            'form' => $form->createView(),
            'institution' => $institution,
        ]);
    }
}
