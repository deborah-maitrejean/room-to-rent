<?php

namespace App\Controller;

use App\Entity\PasswordUpdate;
use App\Entity\User;
use App\Form\AccountType;
use App\Form\PasswordUpdateType;
use App\Form\RegistrationType;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AccountController extends AbstractController
{
    /**
     * Permet d'afficher le formulaire de connexion et de se connecter
     * @Route("/login", name="account_login")
     * @param AuthenticationUtils $utils
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $lastUsername = $utils->getLastUsername();

        return $this->render('account/login.html.twig', [
            'hasError' => $error !== null,
            'username' => $lastUsername,
        ]);
    }

    /**
     * Permet de se déconnecter
     * @Route("/logout", name="account_logout")
     */
    public function logout()
    {
        // ... ne fait rien
    }

    /**
     * Permet d'afficher le formulaire d'inscription
     * @Route("/register", name="account_register")
     * @param Request $request
     * @param ObjectManager $manager
     * @param UserPasswordEncoderInterface $encoder
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function register(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'succes',
                "Votre compte a bien été crée !"
            );
            return $this->redirectToRoute('account_login');
        }

        return $this->render('account/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher et de traiter le formualire de modification de profil
     * @Route("/account/profile", name="account_profile")
     * @param Request $request
     * @param ObjectManager $manager
     * @return \Symfony\Component\HttpFoundation\Response
     * @IsGranted("ROLE_USER")
     */
    public function profile(Request $request, ObjectManager $manager)
    {
        $user = $this->getUser(); // user actuel

        $form = $this->createForm(AccountType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'succes',
                "Les données du profil ont été enregistrées avec succès !"
            );
        }

        return $this->render('account/profile.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de modifier le mot de passe
     *
     * @Route("/account/password-update", name="account_password")
     *
     *
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @param ObjectManager $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @IsGranted("ROLE_USER")
     */
    public function updatePassword(Request $request, UserPasswordEncoderInterface $encoder, ObjectManager $manager) {
        $passwordUpdate = new PasswordUpdate();

        $user = $this->getUser();

        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            // 1. Vérifier que le oldPassword du formulaire soit le même que le password de l'user
            if(!password_verify($passwordUpdate->getOldPassword(), $user->getPassword())){
                // Gérer l'erreur
                $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez tapé n'est pas votre mot de passe actuel !"));
            } else {
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $encoder->encodePassword($user, $newPassword);
                $user->setPassword($hash);

                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    "Votre mot de passe a bien été modifié !"
                );
                return $this->redirectToRoute('home');
            }
        }
        return $this->render('account/password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher le profil de l'utilisateur connecté
     * @Route("/account", name="account_index")
     * @IsGranted("ROLE_USER")
     */
    public function myAccount()
    {
        return $this->render('user/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    /**
     * Permet d'afficher la liste des réservations faites par l'utilisateur
     * @Route("/account/bookings", name="account_bookings")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function bookings() {
        return$this->render('account/bookings.html.twig');
    }
}
