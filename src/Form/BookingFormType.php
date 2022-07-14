<?php

namespace App\Form;

use DateInterval;
use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currentDateTime = new DateTime('now');
        $currentDateTime->setTime($currentDateTime->format("H"), $currentDateTime->format("i"), "00");
        $builder
            ->add('car', ChoiceType::class, [
                'choices' => $options['ownedCars']
            ])
            ->add('start_time', DateTimeType::class, [
                'widget' => 'single_text',
                'data' => $currentDateTime,
                'years' => [date('Y'), date('Y')+1],
                'attr' => [
                    'min' => $currentDateTime->format('Y-m-d\TH:i'),
                    'max' => (clone $currentDateTime)->add(new DateInterval('P1Y'))->format('Y-m-d\TH:i')
                ]
            ])
            ->add('duration',NumberType::class, [
                'attr' => [
                    'max'  => 10080,
                    'min' => 1
                ]
            ])
            ->add('plug', NumberType::class, [
                'data' => $options['plugId'] ?: null,
                'attr' => [
                    'min' => 0
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'ownedCars'  => [],
            'plugId'     => null
        ]);

        $resolver->setAllowedTypes('ownedCars', array());
        $resolver->setAllowedTypes('plugId', 'integer');
    }
}