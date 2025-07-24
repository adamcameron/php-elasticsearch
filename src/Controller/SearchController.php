<?php

namespace App\Controller;

use App\Service\ElasticsearchAdapter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'search')]
    public function search(Request $request, ElasticsearchAdapter $adapter): Response
    {
        $query = $request->query->get('q', '');
        $results = [];

        if ($query !== '') {
            $results = $adapter->searchByString($query);
        }

        return $this->render('search/search.html.twig', [
            'query' => $query,
            'results' => $results,
        ]);
    }
}
