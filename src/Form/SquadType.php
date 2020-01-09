<?php

namespace App\Form;

use App\Entity\Squad;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
                'attr' => ['class' => 'form-control']
            ])
            ->add('unit1',null,[
                'attr' => ['class' => 'form-control unit-form'],
                'label' => 'The first unit of the squad'
            ])
            ->add('unit2',null,[
                'attr' => ['class' => 'form-control unit-form'],
                'label' => 'The first unit of the squad'
            ])
            ->add('unit3',null,[
                'attr' => ['class' => 'form-control unit-form'],
                'label' => 'The first unit of the squad'
            ])
            ->add('unit4',null,[
                'attr' => ['class' => 'form-control unit-form'],
                'label' => 'The first unit of the squad'
            ])
            ->add('unit5',null,[
                'attr' => ['class' => 'form-control unit-form'],
                'label' => 'The first unit of the squad'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }
}
