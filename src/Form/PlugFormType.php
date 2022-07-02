<?php

namespace App\Form;

use App\Entity\Plugs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlugFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('connector_type', TextType::class, [
                'attr' => [
                    'maxlength' => 10
                ]
            ])
            ->add('max_output', NumberType::class, [
                'attr' => [
                    'max'  => 99999,
                    'min' => 0
                ]
            ])
            ->add('status', CheckboxType::class, [
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Plugs::class,
        ]);
    }
}