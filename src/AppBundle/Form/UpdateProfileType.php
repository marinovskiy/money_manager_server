<?php

namespace AppBundle\Form;

use AppBundle\Entity\Account;
use AppBundle\Entity\Currency;
use AppBundle\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, ['label' => 'Імя'])
            ->add('lastName', TextType::class, ['label' => 'Прізвище'])
//            ->add('email', EmailType::class, ['label' => 'Електронна пошта'])
            ->add('gender', ChoiceType::class, [
                'label' => 'Стать',
                'expanded' => true,
                'choices' => [ucfirst(User::GENDER_MALE) => User::GENDER_MALE, ucfirst(User::GENDER_FEMALE) => User::GENDER_FEMALE]
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