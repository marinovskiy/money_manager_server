<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 5/11/17
 * Time: 9:15 PM
 */

namespace AppBundle\Form\Account;

use AppBundle\Entity\Account;
use AppBundle\Entity\Currency;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddAccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Назва'])
            ->add('description', TextareaType::class, ['required' => false, 'label' => 'Опис'])
            ->add('currency', EntityType::class, ['label' => 'Грошова одиниця', 'class' => Currency::class]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
            'data_class' => Account::class,
        ));
    }
}