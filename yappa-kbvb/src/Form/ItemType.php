<?php
/**
 * Created by PhpStorm.
 * User: alessio
 * Date: 14/12/2017
 * Time: 15:16
 */

namespace App\Form;

use App\Entity\Items;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', TextType::class, array(
                'attr'=>array(
                    'class'  => 'form-control',
                ),
                'required' => false
            ))
            ->add('imagelink', FileType::class, array(
                'attr'=>array(
                    'class'  => 'form-control'
                ),
                'multiple'=>false,
                'required' => false,
                'data_class' => null
            ))
            ->add('active', CheckboxType::class, array(
                'attr'=>array(
                    'class'  => 'form-control',
                ),
                'required' => false
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Save',
                'attr'=>array(
                    'class'=>'btn btn-lg btn-success'
                )
            ))
        ;
    }

    public function getName()
    {
        return 'item_form';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Items::class,
        ));
    }
}