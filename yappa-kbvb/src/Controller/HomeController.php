<?php
/**
 * Created by PhpStorm.
 * User: alessio
 * Date: 05/12/2017
 * Time: 14:23
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    public function index()
    {
        return $this->render('Layouts/Main_Layout.html.twig', array());
    }
}