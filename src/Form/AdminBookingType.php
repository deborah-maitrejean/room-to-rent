<?php

namespace App\Form;

use App\Entity\Ad;
use App\Entity\Booking;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminBookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('endDate', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('comment')

            /*  représente une relation avec l'entité User, avec l'utilisateur
                c'est un type de champ qui hérite du ChoiceType (select list, raido button...)
                le but ici est de permettre de choisir parmis plusieurs entités,
                par exemple parmis plusieurs utilisateurs, dans une liste déroulante
            */
            ->add('booker', EntityType::class, [
                // quelle est la classe qui est utilisée :
                'class' => User::class,
                // qu'est-ce qu'il y aura dans cette liste déroulante :
                //'choice_label' => 'fullName',
                'choice_label' => function($user) {
                    // passe l'utilisateur à cette fonction, et à chaque utilisateur en fait son label
                    return $user->getFirstName() . " " .strtoupper($user->getLastName());
                },
            ])
            ->add('ad', EntityType::class, [
                'class' => Ad::class, // peut être personnalisé, on peut utiliser avec requête dql
                'choice_label' => 'title',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
