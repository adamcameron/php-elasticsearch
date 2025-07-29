<?php

namespace App\Controller;

use App\Service\VersionService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct(
        private readonly VersionService $versionService,
        private readonly LoggerInterface $messagingLogger
    ) {
    }

    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render(
            'home/index.html.twig',
            [
                'environment' => $this->getParameter('kernel.environment'),
                'instanceId' => getenv('POD_NAME') ?: getenv('HOSTNAME') ?: 'unknown',
                'dbVersion' => $this->versionService->getVersion(),
            ]
        );
    }

    #[Route('/log-test', name: 'log-test')]
    public function logTest(Request $request): Response
    {
        $message = $request->query->get('message') ?? 'Default web request log message';

        $this->messagingLogger->info($message);

        return new Response($message);
    }

}
