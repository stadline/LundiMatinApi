<?php
/**
 * Created by PhpStorm.
 * User: valentin
 * Date: 03/08/15
 * Time: 14:52
 */


namespace Stadline\TasksBundle\Command;

use Stadline\FrontBundle\StadlineFrontBundle;
use Stadline\SugarCRMBundle\Service\Client;
use Stadline\TasksBundle\Entity\Logger;
use Stadline\TasksBundle\Manager\InvoiceHandlerManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Stadline\FrontBundle\Service\SoapService;

/**
 * Cette tache permet d'attribuer un numero de facture à toutes les affaires
 *
 * Class AssignFactureCommand
 * @package Stadline\TasksBundle\Command
 */
class AssignFactureCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('stadline:assignfacture')
            ->setDescription('Assigner les numeros de factures de LundiMatin aux affaires de Sugar');

    }

    /**
     * Execute the task
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $manager = $container->get("stadline_task.assign_facture");
        $manager->executeAssignFactureCommand($output);
    }
}