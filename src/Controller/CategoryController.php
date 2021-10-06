<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProgramRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

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
        $categories = $categoryRepository->findAll();

        return $this->json(
            $categories,
            200, [],
            [AbstractNormalizer::GROUPS => ['rest']]
        );
    }

    /**
     * @Route ("/{categoryName}", methods={"GET"}, name="show")
     * @param CategoryRepository $categoryRepository
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
}
