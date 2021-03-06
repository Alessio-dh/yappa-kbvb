<?php
/**
 * Created by PhpStorm.
 * User: alessio
 * Date: 07/12/2017
 * Time: 16:07
 */

namespace App\Command;

use App\Entity\Members;
use App\Service\MemberService;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Filesystem\Filesystem;

class CsvImport extends Command
{
    private $em;
    private $memberService;

    public function __construct(EntityManagerInterface $em,MemberService $memberService)
    {
        parent::__construct();

        $this->em = $em;
        $this->memberService = $memberService;
    }

    protected function configure()
    {
        $this
            ->setName('csv:import')
            ->setDescription('Imports the csv file for filling in members')
            ->addArgument('filepath', InputArgument::OPTIONAL, 'Where is the csv file located at?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Attempting to import CSV member file');

        $filepath = $input->getArgument('filepath');

        if($filepath === null){
            $reader = Reader::createFromPath("%kernel.root_dir%/../src/Data/members.csv",'r');
        }else{
            $fs = new Filesystem();

            if($fs->exists($filepath)){
                $reader = Reader::createFromPath($filepath,'r');
            }else{
                $io->error('Given filepath is not a file');
                exit;
            }
        }
        $reader->setHeaderOffset(0);

        $io->progressStart(iterator_count($reader));
        foreach ($reader as $row){
            $this->memberService->addMember($row['id_membership'],$row['birthdate']);
            $io->progressAdvance();
        }
        $io->progressFinish();
        $io->success('Members are imported!');
    }
}