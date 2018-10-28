<?php

namespace App\Controller;

#use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\AdRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller
{
    /**
     * @Route("/", name="home")
     * @param AdRepository $adRepo
     * @param UserRepository $userRepo
     * @return Response
     */
    public function home(AdRepository $adRepo, UserRepository $userRepo)
    {
        return $this->render('home/index.html.twig', [
            'ads' => $adRepo->findBestAds(3),
            'users' => $userRepo->findBestUsers(2),
        ]);
    }
}
