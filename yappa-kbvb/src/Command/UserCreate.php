<?php
/**
 * Created by PhpStorm.
 * User: alessio
 * Date: 09/12/2017
 * Time: 18:05
 */

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserCreate extends Command
{
    private $em;
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $em,UserPasswordEncoderInterface $passwordEncoder)
    {
        parent::__construct();

        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;

    }

    protected function configure()
    {
        $this
            ->setName('user:create')
            ->setDescription('Can import the given user in the databse for access to the admin dashboard')
            ->addArgument('username', InputArgument::REQUIRED, 'Username you would like to have')
            ->addArgument('password', InputArgument::REQUIRED, 'Password for this user')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Attempting to create user account');

        $username = $input->getArgument('username');
        $passwordPlain = $input->getArgument('password');

        if($this->isAlreadyIn($username)){
            $io->error('A user with this username already exists');
            exit;
        }else{
            $user=new User();
            $password = $this->passwordEncoder->encodePassword($user, $passwordPlain);
            $user->setUsername($username);
            $user->setPassword($password);
            $this->em->persist($user);
            $this->em->flush();
        }

        $io->success('User is created with username '.$username.' !');
    }

    private function isAlreadyIn($username){
        $user = $this->em->getRepository(User::class)->findOneBy(array('username'=>$username));

        if($user == null){
            return false;
        }else{
            return true;
        }
    }
}