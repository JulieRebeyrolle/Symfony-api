<?php

namespace App\Controller;

use App\Entity\Program;
use App\Repository\CategoryRepository;
use App\Repository\ProgramRepository;
use App\Repository\SeasonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
     * @param Program $program
     * @param ProgramRepository $programRepository
     * @return Response
     */
    public function show(Program $program, ProgramRepository $programRepository): Response
    {
        if (!$program) {
            return $this->json(['success' => false, 'status' => '404', 'error' => 'Program Not Found'],404);
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
     * @param SeasonRepository $seasonRepository
     * @param ProgramRepository $programRepository
     * @return Response
     */
    public function seasonShow(int $programId, int $seasonId, SeasonRepository $seasonRepository, ProgramRepository $programRepository, ): Response
    {
        $program = $programRepository->findOneBy(['id' => $programId]);
        if (!$program) {
            return $this->json(['success' => false, 'status' => '404', 'error' => 'Program Not Found'],404);
        }

        $season = $seasonRepository->findOneBy(['id' => $seasonId]);
        if (!$season) {
            return $this->json(['success' => false, 'status' => '404', 'error' => 'Season Not Found'],404);
        }

        return $this->json(
            ['program' => $program, 'season' => $season],
            200, [],
            [AbstractNormalizer::IGNORED_ATTRIBUTES => ['program', 'seasons'], AbstractNormalizer::GROUPS => ['rest']]
        );
    }

    /**
     * @Route ("/", methods={"POST"}, name="create")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return Response
     */
    public function new(Request $request, ValidatorInterface $validator, CategoryRepository $categoryRepository, SerializerInterface $serializer): Response
    {
//        $content = json_decode($request->getContent(), true);
//        $program = new Program();
//        $program->setTitle($content['title'])->setSummary($content['summary']);
//
//        if ($categoryRepository->findOneBy(['name' => $content['category']])) {
//            $program->setCategory($categoryRepository->findOneBy(['name' => $content['category']]));
//        } else {
//            return $this->json(['success' => false, 'status' => '404', 'errors' => 'Catégorie non trouvée'],404);
//        }
//
//        if (isset($content['poster'])) {
//            $program->setPoster($content['poster']);
//        }
        $encoders = [new JsonEncoder()];
        $normalizer = new ObjectNormalizer(null, null, null, new ReflectionExtractor());
        $serializer = new Serializer([$normalizer], $encoders);

        $program = $serializer->deserialize($request->getContent(), Program::class, 'json');

        dd($program);

        $entityManager = $this->getDoctrine()->getManager();

        $errors = $validator->validate($program);

        if (count($errors) > 0) {
            return $this->json(['success' => false, 'status' => '400', 'errors' => $errors],400);
        }

        $entityManager->persist($program);
        $entityManager->flush();

        return $this->json(["success" => true, "data" => $program], 201, [], [AbstractNormalizer::GROUPS => ['rest']]);
    }
}
