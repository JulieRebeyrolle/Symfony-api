<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\ProgramRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/category", name="category_")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route ("/", methods={"GET"}, name="index")
     * @param CategoryRepository $categoryRepository
     * @return Response
     */
    public function index(CategoryRepository $categoryRepository): Response
    {

        return $this->json(
            $categoryRepository->findAll(),
            200, [],
            [AbstractNormalizer::GROUPS => ['rest']]
        );
    }

    /**
     * @Route ("/{categoryName}", methods={"GET"}, name="show")
     * @param string $categoryName
     * @param CategoryRepository $categoryRepository
     * @param ProgramRepository $programRepository
     * @return Response
     */
    public function show(string $categoryName,
                         CategoryRepository $categoryRepository,
                         ProgramRepository $programRepository): Response
    {
        $category = $categoryRepository->findOneBy(['name' => $categoryName]);

        if (!$category) {
            throw $this->createNotFoundException('Category ' . $categoryName . ' not found');
        }
        $programs = $programRepository->findBy(['category' => $category], ['id' => 'DESC'], 3);

        if (!$programs) {
            throw $this->createNotFoundException('No program found for : ' . $categoryName);
        }

        return $this->json(
            ['category' => $category, 'programs' => $programs],
            200, [],
            [AbstractNormalizer::IGNORED_ATTRIBUTES => ['category'], AbstractNormalizer::GROUPS => ['rest']]
        );
    }

    /**
     * @Route ("/", methods={"POST"}, name="create")
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @return Response
     */
    public function new(Request $request, SerializerInterface $serializer, ValidatorInterface $validator): Response
    {
        try {
            $category = $serializer->deserialize($request->getContent(), Category::class, 'json');
            $entityManager = $this->getDoctrine()->getManager();

            $errors = $validator->validate($category);

            if (count($errors) > 0) {
                return $this->json(['success' => false, 'status' => '400', 'errors' => $errors],400);
            }

            $entityManager->persist($category);
            $entityManager->flush();

            return $this->json(["success" => true, "data" => $category], 201, [], [AbstractNormalizer::GROUPS => ['rest'], AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT ]);
        } catch (NotEncodableValueException $e) {
            return $this->json(['success' => false, 'status' => '400', 'error' => $e->getMessage()],400);
        }
    }
}
