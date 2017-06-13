<?php

namespace AppBundle\Form;

use AppBundle\Entity\Currency;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddCurrencyType extends AbstractType
{
    public
    function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Назва'])
            ->add('symbol', TextType::class, ['label' => 'Символ']);
    }

    public
    function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
            'data_class' => Currency::class,
        ));
    }
}