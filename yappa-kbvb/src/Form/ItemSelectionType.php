<?php
/**
 * Created by PhpStorm.
 * User: alessio
 * Date: 17/12/2017
 * Time: 15:20
 */

namespace App\Form;


use App\Entity\Items;
use App\Service\ItemService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItemSelectionType extends AbstractType
{
    private $itemService;

    public function __construct(ItemService $itemService)
    {
        $this->itemService = $itemService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('item', EntityType::class, array(
                'class'  => Items::class,
                'choices' =>$this->itemService->getActiveItems(),
            ))
            ->add('save', SubmitType::class, array('label' => 'Verzenden','attr'=>array('class'=>'pulseBtn')))
        ;
    }

    public function getName()
    {
        return 'item_selection_form';
    }
}