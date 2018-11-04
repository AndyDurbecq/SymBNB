<?php

namespace App\Form;

use App\Entity\Ad;
use App\Form\ImageType;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AdType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title', 
                TextType::class, 
                $this->getConfiguration("Titre", "Titre de votre annonce"))
            ->add(
                'slug', 
                TextType::class, 
                $this->getConfiguration("Adresse URL", "L'adresse url (automatique)", ['required' => false]))
            ->add(
                'coverImage', 
                UrlType::class, 
                $this->getConfiguration("Url de l'image", "Entrez une url de votre image"))
            ->add(
                'introduction', 
                TextType::class, 
                $this->getConfiguration("Introduction", "Donner une description courte de cotre annonce"))
            ->add(
                'content', 
                TextareaType::class, 
                $this->getConfiguration("Description détaillée", "Description qui donne envie"))
            ->add(
                'rooms', 
                IntegerType::class,
                 $this->getConfiguration("Nombre de chambre", ""))
            ->add(
                'price', 
                MoneyType::class, 
                $this->getConfiguration("Prix par nuit", "Indiquez le prix de la nuit"))            
            ->add(
                'images', 
                CollectionType::class,
                [
                    'entry_type' => ImageType::class,
                    'allow_add' => true,
                    'allow_delete' => true
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
