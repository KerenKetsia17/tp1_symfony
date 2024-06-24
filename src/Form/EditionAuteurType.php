<?php

namespace App\Form;


use App\Entity\Author;
use App\Entity\Book;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class EditionAuteurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => ['placeholder' => 'Entrer le nom complet', 'autocomplete' => 'off']
            ])
            ->add('livres', EntityType::class, [
                'class' => Book::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'label' => 'Livres'
            ])
            ->add('enregistrer', SubmitType::class, [
                'label' => 'Ajouter',
                'attr' => ['class' => 'btn btn-primary']
            ])
            ->add('annuler', ResetType::class, [
                'label' => 'Annuler',
                'attr' => ['class' => 'btn btn-danger']
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here

            'data_class' => Author::class,
        ]);
    }
}
