<?php
/**
 * Created by PhpStorm.
 * User: alessio
 * Date: 16/12/2017
 * Time: 13:29
 */

namespace App\Form;


use App\Entity\Items;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItemDeleteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', TextType::class, array(
                'attr'=>array(
                    'class'  => 'form-control idOfItem',
                    'hidden'=>true
                ),
                'label'=>false,
                'required' => true
            ))
            ->add('delete', SubmitType::class, array('label' => 'Delete','attr'=>array('class'=>'btn btn-danger')))
        ;
    }

    public function getName()
    {
        return 'item_delete_form';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Items::class,
        ));
    }
}