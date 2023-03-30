<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Formation;
use App\Entity\Playlist;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('publishedAt', DateType::class, [
                "label"=>"Date de publication",
                "attr"=>[
                    "max"=>date('Y-m-d'),
                    ],
                'widget' => 'single_text',
                "required"=>"required"
            ])
            ->add('title', TextType::class, [
                "label"=>"Titre",
                "required"=>"required"
            ])
            ->add('description', TextareaType::class, [
                "label"=>"Description",
                "required"=>false,
                "attr"=>[
                    "style"=>"height: 20vh;"
                    ]
            ])
            ->add('videoId', TextType::class, [
                "label"=>"Id de vidéo",
                "required"=>"required"
            ])
            ->add('playlist', EntityType::class, [
                "label"=>"Playlist",
                'class'=>Playlist::class,
                'multiple'=>false,
                'expanded'=>false
            ])
            ->add('categories', EntityType::class, [
                "label"=>"Categorie(s)",
                'class'=>Categorie::class,
                'multiple'=>true,
                'expanded'=>false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}
