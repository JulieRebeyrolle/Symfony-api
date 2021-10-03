<?php

namespace App\Controller;

use App\Entity\Program;
use App\Repository\ProgramRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/program", name="program_")
 */

class ProgramController extends AbstractController
{
    /**
     * @Route ("/", methods={"GET"}, name="index")
     * @param ProgramRepository $programRepository
     * @return Response
     */
    public function index(ProgramRepository $programRepository): Response
    {
        $programs = $programRepository->programFindAll();

         return $this->json($programs);

//        $encoders = [new JsonEncoder()];
//        $normalizers = [new ObjectNormalizer()];
//        $serializer = new Serializer($normalizers, $encoders);
//        $jsonContent = $serializer->serialize($programs, 'json');
//        $response = new Response($jsonContent);
//        $response->headers->set('Content-Type', 'application/json');
//        return $response;
    }
}
