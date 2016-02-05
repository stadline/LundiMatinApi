<?php
/**
 * Created by PhpStorm.
 * User: valentin
 * Date: 30/07/15
 * Time: 09:28
 */

namespace Stadline\TasksBundle\Command;



use Stadline\FrontBundle\StadlineFrontBundle;
use Stadline\TasksBundle\Entity\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Stadline\TasksBundle\Manager\InvoiceHandlerManager;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Stadline\FrontBundle\Service\SoapService;

/**
 * Cette tache permet d'attribuer un numero de facture à toutes les affaires
 *
 * Class StructurePhaseCommand
 * @package Stadline\TasksBundle\Command
 */
class StructurePhaseCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('stadline:structurephase')
            ->setDescription('Mettre à jour les phases de ventes des affaires sur Sugar');
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
        $manager = new InvoiceHandlerManager();
        $manager->executeStructurePhaseCommand($container, $input, $output);
    }

}