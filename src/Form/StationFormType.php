<?php

namespace App\Form;

use App\Entity\Stations;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('plusCode')
            ->add('lat', NumberType::class, [
                'attr' => [
                    'step' => '0.000000000000001'
                ]
            ])
            ->add('lon', NumberType::class, [
                'attr' => [
                    'step' => '0.000000000000001'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Stations::class,
        ]);
    }
}