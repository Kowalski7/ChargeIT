<?php

namespace App\Form;

use App\Entity\Stations;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;

class StationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('plusCode')
            ->add('lat', NumberType::class, [
                'attr' => [
                    'min'  => -90,
                    'max'  => 90
                ],
                'constraints' => [
                    new GreaterThanOrEqual(
                        -90,
                        options: ['message' => 'Latitude must be at least -90']
                    ),
                    new LessThanOrEqual(
                        90,
                        options: ['message' => 'Latitude must be at most 90']
                    )
                ]
            ])
            ->add('lon', NumberType::class, [
                'attr' => [
                    'min'  => -180,
                    'max'  => 180
                ],
                'constraints' => [
                    new GreaterThanOrEqual(
                        -180,
                        options: ['message' => 'Longitude must be at least -180']
                    ),
                    new LessThanOrEqual(
                        180,
                        options: ['message' => 'Longitude must be at most 180']
                    )
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