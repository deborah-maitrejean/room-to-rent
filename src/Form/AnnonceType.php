<?php

namespace App\Form;

use App\Entity\Ad;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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
     * @param array $options
     * @return array
     */
    private function getConfiguration($label, $placeholder, $options = [])
    {
        return array_merge( [ // array_merge va fusionner le tableau avec le tableau des options
            'label' => $label,
            'attr' => [
                'placeholder' => $placeholder,
            ]
        ], $options);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class,
                $this->getConfiguration("Titre", "Tapez le titre de l'annonce")
            )
            ->add('slug', TextType::class,
                $this->getConfiguration("Chaîne URL", "Adresse Web (automatique)", [
                    'required' => false,
                ])
            )
            ->add('coverImage', UrlType::class,
                $this->getConfiguration("URL de l'image principale", "Donnez l'adresse d'une image")
            )
            ->add('introduction', TextType::class,
                $this->getConfiguration("Introduction", "Donnez une description globale")
            )
            ->add('content', TextareaType::class,
                $this->getConfiguration("Contenu", "Description détaillée")
            )
            ->add('price', MoneyType::class,
                $this->getConfiguration("Prix par nuit", "Indiquez le prix")
            )
            ->add('rooms', IntegerType::class,
                $this->getConfiguration("Nombre de chambres", "Nombre de chambres disponibles")
            )
            ->add('save', SubmitType::class, [
                'label' => 'Valider',
                'attr' => [
                    'class' => 'btn btn-primary',
                ]
            ])
            ->add(
                'images',
                CollectionType::class,
                [
                    'entry_type' => ImageType::class, // quel est le champ ou le formulaire qu'il faut répéter ? entry est une entrée, un élément de la collection
                    'allow_add' => true, // permet de préciser si l'on a le droit d'ajouter de nouveaux éléments, signifie qu'on peux ajouter de nouveaux éléments à la collection
                    'allow_delete' => true, // autorise la suppression des éléments du collectionType
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
