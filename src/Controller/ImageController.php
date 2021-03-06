<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Image;
use App\Entity\Tag;
use App\Repository\CategoryRepository;
use App\Repository\ImageRepository;
use App\Form\ImageType;
use App\Repository\TagRepository;
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
    public function index(Request $request, int $page, ImageRepository $images, TagRepository $tagRepository, CategoryRepository $categories)
    {

        if (0 === $request->query->count()) {
            return $this->render('image/cerca.html.twig', [
                'tags' => $tagRepository->findAll(),
                'categories' => $categories->findAll(),
            ]);
        }


        $tags = [];
        if ($request->query->get('tags')) {
            $searchTags = explode(',', $request->query->get('tags'));
            foreach ($searchTags as $tagName){
                $tags[] = $tagRepository->findOneBy(['name' => $tagName]);
            }
        }

        $category = null;
        if ($request->query->has('category')) {
            $category = $categories->find($request->query->get('category'));
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

        $result = $images->findBySearchQuery($page, $tags, $category, $rawQuery, $dateFrom, $dateTo);

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
     * @Route("/upload/{id<\d+>}", name="image_upload_to")
     */
    public function upload(Request $request, Category $category = null)
    {
        $image = new Image();
        $options = [];

        //upload from eurostep
        if ($category !== null) {
            $image->setCategory($category);
            $ordine = $request->get('ordine');
            $lotto= $request->get('lotto');
            $ordinelotto = $ordine . "_" .$lotto;
            $operatore = $request->get('operatore');
            $note = $request->get('note');
            $soluzione = $request->get('soluzione');

            $options = [
                'category_disabled' => true,
                'description' => "[$operatore] Ordine: $ordinelotto $note \nSoluzione: $soluzione"
            ];

        }

        $form = $this->createForm(ImageType::class, $image, $options);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $image = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($image);
            $entityManager->flush();

            $this->addFlash('success', 'La tua foto è stata caricata.');

            return $this->redirectToRoute('home');
        }

        return $this->render('image/upload.html.twig', [
            'form' => $form->createView(),
        ]);

    }

    /**
     * @Route("/edit/{id}", name="image_edit")
     */
    public function edit(Request $request, Image $image)
    {
        $form = $this->createForm(ImageType::class, $image, ['image_required' => false]);

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $image = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($image);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('image/edit.html.twig', [
            'form' => $form->createView(),
        ]);

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

        $this->addFlash('warning', 'Foto cancellata.');

        return $this->redirectToRoute('home');
    }

}
