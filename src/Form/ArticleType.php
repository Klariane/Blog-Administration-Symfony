<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder //construction de formulaire
            ->add('title')
            ->add('content')
            ->add('imageFile', FileType::class, ['required'=>false])
            ->add('category', EntityType::class,[ //category est une entité qui existe dans lentite article.ce champp est une entity
                'class'=>Category::class, // le nom de lentité est Category. cette entity sappele category
                'choice_label'=>'title' //le champ est un menu déroulant qui affiche le titre . choice_label creer le menu déroulant
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
