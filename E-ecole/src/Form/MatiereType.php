<?php

namespace App\Form;

use App\Entity\Enseignant;
use App\Entity\Matiere;
use App\Entity\Promotion;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MatiereType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('enseignant',EntityType::class,[
                'class'=> Enseignant::class,
                'choice_label'=>'nom',
            ])
            ->add('promotion', EntityType::class,[
                'class'=>Promotion::class,
                'choice_label'=>'libelle'
            ])


        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Matiere::class,
        ]);
    }
}
