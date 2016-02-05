<?php
/**
 * Created by PhpStorm.
 * User: valentin
 * Date: 25/08/15
 * Time: 15:12
 */


namespace Stadline\TasksBundle\Command;

use Stadline\FrontBundle\StadlineFrontBundle;
use Stadline\TasksBundle\Entity\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Stadline\FrontBundle\Service\SoapService;



class ManagerTelExportCommand extends ContainerAwareCommand
{


    protected function configure()
    {
        $this
            ->setName('stadline:managertelexport')
            ->setDescription('');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $manager = $container->get('stadline_tasks.ManagerTelExport');
        $contacts = $manager->getDataFromSugar();
        $csv = $manager->generateCsv($contacts);

        // send email.
    }

}