<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ImageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(ImageRepository $imageRepository)
    {
        $images = $imageRepository->findLasts(4);
        $imagesMostViewed = $imageRepository->findMostViewed(4);

        return $this->render('default/home.html.twig', [
            'lasts' => $images,
            'mostViewed' => $imagesMostViewed,
        ]);
    }

    /**
     * This controller is called directly via the render() function in the
     * blog/post_show.html.twig template. That's why it's not needed to define
     * a route name for it.
     *
     * The "id" of the Post is passed in and then turned into a Post object
     * automatically by the ParamConverter.
     */
    public function categoryList(CategoryRepository $categoryRepository)
    {
        $categories = $categoryRepository->findAll();

        return $this->render('default/_image_categories.html.twig', [
            'categories' => $categories,
        ]);
    }
}
