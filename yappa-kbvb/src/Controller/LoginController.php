<?php
/**
 * Created by PhpStorm.
 * User: alessio
 * Date: 08/12/2017
 * Time: 16:16
 */

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class LoginController extends Controller
{
    /**
     * @Route("/login",name="login")
     */
    public function loginUser(Request $request,AuthenticationUtils $authUtils){

        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();

        return $this->render('admin/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));

       /* $user = new User();
        $form = $this->createFormBuilder($user)
            ->add('username', TextType::class,array('label'=>'Gebruikersnaam',
                'attr'=> array(
                    'class' => 'form-control',
                    'placeholder'=>'gebruikersnaam'
                )
            ))
            ->add('password', PasswordType::class,array('label'=>'Wachtwoord',
                'attr'=> array(
                    'class' => 'form-control',
                    'placeholder'=>'Wachtwoord'
                )
            ))
            ->add('save', SubmitType::class, array('label' => 'Log in','attr'=>array('class'=>'pulseBtn')))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            if($this->isAllowed($user->getUsername(),$user->getPassword())){
                return $this->redirectToRoute('admin');
            }else{
                $form->addError(new FormError('De combinatie is niet gevonden in ons systeem, Probeer opnieuw'));
            }
        }

        return $this->render('Admin/login.html.twig',array(
            'form' => $form->createView(),
        ));*/
    }

    private function isAllowed($username,$password){
        $user =  $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(array('username' => $username));
        if($user != null ){
            $encrypter = new BCryptPasswordEncoder(12);
            if($encrypter->isPasswordValid($user->getPassword(),$password,null)){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}