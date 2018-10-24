<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Form\BookingType;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{
    /**
     * @Route("/ad/{slug}/book", name="booking_create")
     * @param Ad $ad
     * @param Request $request
     * @param ObjectManager $manager
     * @return \Symfony\Component\HttpFoundation\Response
     * @IsGranted("ROLE_USER")
     */
    public function book(Ad $ad, Request $request, ObjectManager $manager)
    {
        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser() ; // relié la réservation à l'utilisateur connecté

            $booking->setBooker($user)
                ->setAd($ad)
            ;

            // si les dates ne sont pas disponibles, message d'erreur:
            if (!$booking->isBookableDate()) {
                $this->addFlash(
                  'warning',
                  "Les dates que vous avez choisi sont déjà prises."
                );
            } else {// sinon, enregistrement et redirection
                $manager->persist($booking);
                $manager->flush();

                return $this->redirectToRoute('booking_show', [
                        'id' => $booking->getId(),
                        'withAlert' => true,
                    ]
                );
            }
        }

        return $this->render('booking/book.html.twig', [
            'ad' => $ad,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Permet d'afficher la page d'une réservation
     *
     * @Route("/booking/{id}", name="booking_show")
     *
     * @param Booking $booking
     * @param Request $request
     * @param ObjectManager $manager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(Booking $booking, Request $request, ObjectManager $manager) {
        return $this->render('booking/show.html.twig', [
            'booking' => $booking,
        ]);
    }
}
