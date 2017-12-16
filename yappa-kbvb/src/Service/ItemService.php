<?php
/**
 * Created by PhpStorm.
 * User: alessio
 * Date: 14/12/2017
 * Time: 13:20
 */

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Items;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ItemService
{
    private $em;
    private $container;

    public function __construct(EntityManagerInterface $em,ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    public function getItems(){
        $items =  $this->em->getRepository(Items::class)
            ->findAll();
        return $items;
    }

    public function getItem($id){
        $item =  $this->em->getRepository(Items::class)
            ->findOneBy(array('id'=>$id));
        return $item;
    }

    public function editItem($item){
        try{
            if($item->getImageLink() ===null){
                $this->em->clear();
                $itemOld = $this->em->getReference('App\Entity\Items',$item->getId());
                $itemOld->setDescription($item->getDescription());
                $itemOld->setActive($item->getActive());
                $itemOld->setImageLink($this->getItem($item->getId())->getImageLink());
                $this->em->persist($itemOld);
                $this->em->flush();

            }else{
                $file = $item->getImageLink();
                $dateNow = new \DateTime();
                $dateNow = $dateNow->format("YmdHis");
                $newFileName = rand(1,9999).$dateNow.rand(1,9999).'.'.$file->guessExtension();

                $file->move(
                    $this->container->getParameter('item_image_directory'),
                    $newFileName
                );

                // Can unlink old file here but not needed because we will not be working with that many items

                $item->setImagelink($newFileName);

                $this->em->persist($item);
                $this->em->flush();
            }
            return true;
        }catch (\Exception $ex){
            return false;
        }
    }

    public function addItem($data){
            $file = $data->getImageLink();
            $dateNow = new \DateTime();
            $dateNow = $dateNow->format("YmdHis");
            $newFileName = rand(1,9999).$dateNow.rand(1,9999).'.'.$file->guessExtension();

            $file->move(
                $this->container->getParameter('item_image_directory'),
                $newFileName
            );

            $data->setImagelink($newFileName);

            $this->em->persist($data);
            $this->em->flush();
    }

    public function deleteItem($id){
        $item = $this->getItem($id);
        $this->em->remove($item);
        $this->em->flush();
    }

    public function getActiveItems(){
        $items =  $this->em->getRepository(Items::class)
            ->findBy(array('active'=>1));
        return $items;
    }
}