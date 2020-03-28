<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Service\Entity\Unit as UnitHelper;

class SquadType extends AbstractType
{
    private $unitHelper;

    public function __construct(UnitHelper $unitHelper)
    {
        $this->unitHelper = $unitHelper;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',null,[
                'attr' => ['class' => 'form-control'],
                'label' => 'Nom de l\'équipe'
            ])
            ->add('unit1',null,[
                'attr' => ['class' => 'form-control unit-form'],
                'label' => '1ère unité de l\'équipe'
            ])
            ->add('unit2',null,[
                'attr' => ['class' => 'form-control unit-form'],
                'label' => '2ème unité de l\'équipe'
            ])
            ->add('unit3',null,[
                'attr' => ['class' => 'form-control unit-form'],
                'label' => '3ème unité de l\'équipe'
            ])
            ->add('unit4',null,[
                'attr' => ['class' => 'form-control unit-form'],
                'label' => '4ème unité de l\'équipe'
            ])
            ->add('unit5',null,[
                'attr' => ['class' => 'form-control unit-form'],
                'label' => '5ème unité de l\'équipe'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }
}
