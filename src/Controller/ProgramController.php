<?php

namespace App\Controller;

use App\Repository\ProgramRepository;
use App\Repository\SeasonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

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
        return $this->json(
            $programRepository->findAll(),
            200, [],
            [AbstractNormalizer::GROUPS => ['rest'], AbstractNormalizer::IGNORED_ATTRIBUTES => ['program', 'season']]
        );
    }

    /**
     * @Route ("/{id}", methods={"GET"}, name="show")
     * @param int $id
     * @param ProgramRepository $programRepository
     * @return Response
     */
    public function show(int $id, ProgramRepository $programRepository): Response
    {
        $program = $programRepository->findOneBy(['id' => $id]);

        if (!$program) {
            return $this->json(['status' => '404', 'error' => 'Program Not Found'],404);
        }

        return $this->json(
            $program,
            200, [],
            [AbstractNormalizer::IGNORED_ATTRIBUTES => ['program', 'season'], AbstractNormalizer::GROUPS => ['rest']]
        );
    }

    /**
     * @Route ("/{programId}/season/{seasonId}", methods={"GET"}, name="season_show")
     * @param int $programId
     * @param int $seasonId
     * @param ProgramRepository $programRepository
     * @return Response
     */
    public function seasonShow(int $programId, int $seasonId, SeasonRepository $seasonRepository, ProgramRepository $programRepository): Response
    {
        $program = $programRepository->findOneBy(['id' => $programId]);
        if (!$program) {
            return $this->json(['status' => '404', 'error' => 'Program Not Found'],404);
        }

        $season = $seasonRepository->findOneBy(['id' => $seasonId]);
        if (!$season) {
            return $this->json(['status' => '404', 'error' => 'Season Not Found'],404);
        }

        return $this->json(
            ['program' => $program, 'season' => $season],
            200, [],
            [AbstractNormalizer::IGNORED_ATTRIBUTES => ['program', 'seasons'], AbstractNormalizer::GROUPS => ['rest']]
        );
    }
}
