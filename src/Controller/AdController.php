<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Repository\AdRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
