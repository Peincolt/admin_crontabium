<?php

namespace App\Form;

use App\Entity\UserDemand;
use App\Service\Entity\Guild as GuildHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserDemandType extends AbstractType
{
    private $guildHelper;

    public function __construct(GuildHelper $guildHelper)
    {
        $this->guildHelper = $guildHelper;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match',
                'required' => true,
                'first_options' => ['label' => 'Enter your futur password'],
                'second_options' => ['label' => 'Repeat your futur password']
            ])
            ->add('email', EmailType::class)
            ->add('guild', ChoiceType::class, [
                'choices' => $this->guildHelper->getFormGuild(),
                'label' => 'Choose your guild'
            ])
            ->add('role', ChoiceType::class, [
                'choices' => [
                    'Member' => 'USER',
                    'Officer' => 'ADMIN'
                ],
                'label' => 'What\'s your status in your guild ?'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserDemand::class,
        ]);
    }
}
