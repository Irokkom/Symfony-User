<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('content')
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name', // Utilise 'name' si tu as un champ name dans Category
                'multiple' => true,     // Permet de sélectionner plusieurs catégories
                'required' => false,    // Rend le champ facultatif
                'placeholder' => 'Aucune catégorie', // Option vide par défaut
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
