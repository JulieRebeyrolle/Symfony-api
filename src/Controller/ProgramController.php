<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProgramController extends AbstractController
{
    /**
     * @Route ("/program/", name="program_index", methods={"GET"})
     * @return Response
     */
    public function index(): Response
    {
        return new Response(
            '', 200
        );
    }
}
