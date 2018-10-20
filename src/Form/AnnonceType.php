<?php

namespace App\Form;

use App\Entity\Ad;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnnonceType extends AbstractType
{
    /**
     * permet d'avoir la configuration de base d'un champ
     * @param string $label
     * @param string $placeholder
     * @return array
     */
    private function getconfiguration($label, $placeholder)
    {
        return [
            'label' => $label,
            'attr' => [
                'placeholder' => $placeholder,
            ]
        ];
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, $this->getconfiguration("Titre", "Tapez le titre de l'annonce"))
            ->add('slug', TextType::class, $this->getconfiguration("Chaîne URL", "Adresse Web (automatique)"))
            ->add('coverImage', UrlType::class, $this->getconfiguration("URL de l'image principale", "Donnez l'adresse d'une image"))
            ->add('introduction', TextType::class, $this->getconfiguration("Introduction", "Donnez une description globale"))
            ->add('content', TextareaType::class, $this->getconfiguration("Contenu", "Description détaillée"))
            ->add('price', MoneyType::class, $this->getconfiguration("Prix par nuit", "Indiquez le prix"))
            ->add('rooms',IntegerType::class, $this->getconfiguration("Nombre de chambres", "Nombre de chambres disponibles"))
            ->add('save', SubmitType::class, [
                'label' => 'Créer la nouvelle annonce',
                'attr' => [
                    'class' => 'btn btn-primary',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
