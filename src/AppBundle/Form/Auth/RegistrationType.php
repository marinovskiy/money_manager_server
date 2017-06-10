<?php

namespace AppBundle\Form\Auth;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, ['label' => 'Імя'])
            ->add('lastName', TextType::class, ['label' => 'Прізвище'])
            ->add('email', EmailType::class, ['label' => 'Електронна пошта'])
            ->add('gender', ChoiceType::class, [
                'label' => 'Стать',
                'expanded' => true,
                'choices' => User::GENDER_TITLES,
//                'choices' => [ucfirst(User::GENDER_MALE) => User::GENDER_MALE, ucfirst(User::GENDER_FEMALE) => User::GENDER_FEMALE]
            ])
//            ->add('platformType', TextType::class)
//            ->add('udid', TextType::class)
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_name' => 'password',
                'second_name' => 'confirmPassword',
                'first_options'  => array('label' => 'Пароль'),
                'second_options' => array('label' => 'Повторіть пароль'),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
            'data_class' => User::class,
        ));
    }
}