<?php
/**
 * Created by PhpStorm.
 * User: alessio
 * Date: 16/12/2017
 * Time: 14:21
 */

namespace App\Form;

use App\Entity\MemberEntry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MemberEntryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('day', IntegerType::class,array('label'=>'Dag',
                'attr'=> array(
                    'class' => 'form-control form-inline',
                    'min' => 1,
                    'max' => 31,
                    'placeholder'=>'Dag'
                )
            ))
            ->add('month', IntegerType::class,array('label'=>'Maand',
                'attr'=> array(
                    'class' => 'form-control form-inline',
                    'min' => 1,
                    'max' => 12,
                    'placeholder'=>'Maand'
                )
            ))
            ->add('year', IntegerType::class,array('label'=>'Jaar',
                'attr'=> array(
                    'class' => 'form-control form-inline',
                    'min' => 1900,
                    'max' => date("Y"),
                    'placeholder'=>'Jaar'
                )
            ))
            ->add('member_id', IntegerType::class,array('label'=>'Lidnummer',
                'attr'=> array(
                    'class' => 'form-control',
                )
            ))
            ->add('save', SubmitType::class, array('label' => 'Volgende','attr'=>array('class'=>'pulseBtn')))
        ;
    }

    public function getName()
    {
        return 'memberEntry_form';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => MemberEntry::class,
        ));
    }
}