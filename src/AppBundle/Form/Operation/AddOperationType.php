<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 5/11/17
 * Time: 9:15 PM
 */

namespace AppBundle\Form\Operation;

use AppBundle\Entity\Category;
use AppBundle\Entity\Operation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddOperationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, ['choices' => Operation::TYPES_TITLES])
            ->add('description', TextareaType::class)
            ->add('sum', IntegerType::class, ['attr' => ['step' => 0.01]])
            ->add('category', EntityType::class, ['class' => Category::class]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
            'data_class' => Operation::class,
        ));
    }
}