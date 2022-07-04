<?php

namespace App\Form;

use App\Entity\Cars;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class CarFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plug_type', TextType::class, [
                'attr' => [
                    'maxlength' => 10
                ]
            ])
            ->add('license_plate', TextType::class, [
                'constraints' => [
                    new Regex(
                        "/([A-Z]{1,2})+\s+((([0-9]{2,3})+\s+([A-Z]{3}))|([0-9]{6}))/",
                        options: [
                            'message' => 'Please enter a valid license plate number'
                        ]
                    )
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cars::class,
        ]);
    }
}