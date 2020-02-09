<?php

namespace App\Controller;

use App\Entity\Image;
use App\Repository\ImageRepository;
use App\Form\ImageType;
use Liip\ImagineBundle\Imagine\Cache\CacheManager as LiipCacheManager;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper as UploaderHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ImageController
 * @package App\Controller
 * @Route("/image")
 */
class ImageController extends AbstractController
{
    /**
     * @Route("/", defaults={"page": "1", "_format"="html"}, methods={"GET"}, name="image_index")
     * @Route("/page/{page<[1-9]\d*>}", defaults={"_format"="html"}, methods={"GET"}, name="image_index_paginated")
     */
    public function index(Request $request, int $page, ImageRepository $images)
    {
        /*$tag = null;
        if ($request->query->has('tag')) {
            $tag = $tags->findOneBy(['name' => $request->query->get('tag')]);
        }

        $category = null;
        if ($request->query->has('category')) {
            $category = $categories->find($request->query->get('category'));
        }

        $latestImage = $images->findAllBy($page, $tag, $category);*/

        if (0 === $request->query->count()) {
            return $this->render('image/cerca.html.twig');
        }

        $rawQuery = $request->get("queryString", '');
        $dateFrom = $request->get("uploadFromDate", null);
        if (!$dateFrom) {
            $dateFrom = date('Y-m-d', date_timestamp_get(new \DateTime('-3  months')));
        }

        $dateTo = $request->get("uploadToDate", null);
        if (!$dateTo) {
            $dateTo = date('Y-m-d', date_timestamp_get(new \DateTime('now')));
        }

        $result = $images->findBySearchQuery($page, $rawQuery, $dateFrom, $dateTo);

        return $this->render('image/index.html.twig', [
            'paginator' => $result,
        ]);
    }

    /**
     * @Route("/view/{id<\d+>}", name="image_view")
     */
    public function addView(Image $image)
    {
        $views = $image->getViews();
        $image->setViews($views + 1);
        $image->setViewAt(new \DateTime('now'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($image);
        $em->flush();

        return new Response($views + 1, 200);
    }

    /**
     * @Route("/upload", name="image_upload")
     */
    public function upload(Request $request)
    {
        $image = new Image();

        $form = $this->createForm(ImageType::class, $image);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $image = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($image);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('image/upload.html.twig', [
            'form' => $form->createView(),
        ]);

    }

    /**
     * @Route("/search", methods={"GET"}, name="image_search")
     */
    public function search(Request $request, ImageRepository $repository)
    {
        $rawQuery = $request->get("queryString", '');
        $dateFrom = $request->get("uploadFromDate", null);
        if (!$dateFrom) {
            $dateFrom = date('Y-m-d', date_timestamp_get(new \DateTime('-3  months')));
        }

        $dateTo = $request->get("uploadToDate", null);
        if (!$dateTo) {
            $dateTo = date('Y-m-d', date_timestamp_get(new \DateTime('now')));
        }

        $result = $repository->findBySearchQuery($rawQuery, $dateFrom, $dateTo);

        if ($request->query->has('queryString')) {
            return $this->render('image/index.html.twig', [
                'paginator' => $result
            ]);
        }

        return $this->render('image/cerca.html.twig');
    }

    /**
     * @Route("/delete/{id}", name="image_delete")
     */
    public function delete(Image $image, LiipCacheManager $cacheManager, UploaderHelper $uploaderBundle)
    {
        // get the UploaderHelper service...
        $resolvedPath = $uploaderBundle->asset($image, 'imageFile');

        $cacheManager->remove($resolvedPath, 'homepage_thumb');

        $em = $this->getDoctrine()->getManager();
        $em->remove($image);
        $em->flush();

//        $this->addFlash('warning', 'DDT cancellato!!');

        return $this->redirectToRoute('home');
    }

}
