<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Image;
use App\Form\AnnonceType;
use App\Repository\AdRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ads_index")
     * @param AdRepository $repo
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(AdRepository $repo) // type hinting, sert à expliciter le type de donnée attendu pour un paramètre
    {
        //$repo = $this->getDoctrine()->getRepository(Ad::class);
        $ads = $repo->findAll();

        return $this->render('ad/index.html.twig', [
            'ads' => $ads,
        ]);
    }

    /**
     * Permet de créer une annonce
     * @Route("/ad/new", name="ads_create")
     * @param Request $request
     * @param ObjectManager $manager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request, ObjectManager $manager)
    {
        $ad = new Ad();
        $form = $this->createForm(AnnonceType::class, $ad);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($ad->getImages() as $image) {
                $image->setAd($ad);
                $manager->persist($image);
            }

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre annonce <strong>test</strong> a été publiée !'
            );

            return $this->redirectToRoute('ad_show', [
                'slug' => $ad->getSlug(), // redirection vers la page de l'annonce que l'on vient de créer
            ]);
        }

        return $this->render('ad/new.html.twig', [
            'annonce_form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/ad/{slug}", name="ad_show")
     * @param $slug
     * @param Ad $ad
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show($slug, Ad $ad)
    {
        // récupère l'annonce qui correspond au slug passé en paramètre
        return $this->render('ad/show.html.twig', [
            'ad' => $ad,
        ]);
    }

    /**
     * Permet d'afficher le formulaire d'édition
     * @Route("/ad/{slug}/edit", name="ad_edit")
     * @param Ad $ad
     * @param Request $request
     * @param ObjectManager $manager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Ad $ad,Request $request, ObjectManager $manager)
    {
        // paramconverter : convertit un paramètre (slug par ex) en entité (ici Ad)
        $form = $this->createForm(AnnonceType::class, $ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($ad->getImages() as $image) {
                $image->setAd($ad);
                $manager->persist($image);
            }

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les modifications de l'annonce <strong>{$ad->getTitle()}</strong> ont été enregistrées."
            );
        }

        return $this->render('ad/edit.html.twig',[
            'annonce_form' => $form->createView(),
            'ad' => $ad,
        ]);
    }
}
