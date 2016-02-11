<?php
/**
 * Created by PhpStorm.
 * User: gaetan
 * Date: 1/28/16
 * Time: 2:26 PM
 */

namespace Stadline\TasksBundle\Tests\Manager;

use Mockery as m;
use Stadline\SugarCRMBundle\Service\GetOpportunities\Opportunity;
use Stadline\TasksBundle\Manager\InvoiceHandlerManager;
use Stadline\TasksBundle\Service\ManagerCommandService;
use Stadline\TasksBundle\Test\WebTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class StructurePhaseCommandTest extends WebTestCase {

    public function testBadMontant(){
        $client = static::createClient();

        $container = $client->getContainer();
        $sugarMock = $container->get("stadline_sugar_crm_client");
        $soapMock = $container->get("stadline_front.soap_service");

        $opportunity1 = new Opportunity();
        $opportunity1->setId('1');
        $opportunity1->setName('Test Lundi Matin');
        $opportunity1->setCreatedAt(new \DateTime());
        $opportunity1->setClosedAt(new \DateTime());
        $opportunity1->setAssignedUserName('UsernameTest');
        $opportunity1->setOpportunityType('test');
        $opportunity1->setCampaignName('test');
        $opportunity1->setLeadSource('test');
        $opportunity1->setAmount(50);
        $opportunity1->setSalesStage('facture');
        $opportunity1->setTypeAffaireC('');
        $opportunity1->setidLMB('C-000000-00001');
        $opportunity1->setnumfact('BLC-00004');
        $sugarMock->addOpportunity($opportunity1);

        $manager = new InvoiceHandlerManager($container);
        $output = $this->getOutputMock();
        $output->shouldReceive('writeln')->with('> Affaire avec numéro LM trouvée pour Test Lundi Matin : Test Lundi Matin')->once()->andReturn(true);
        $output->shouldReceive('writeln')->with('> Test Lundi Matin : erreur montant - pas de mise à jour')->once()->andReturn(true);
        $manager->executeStructurePhaseCommand($output);
    }

    public function testCase16(){
        $client = static::createClient();

        $container = $client->getContainer();
        $sugarMock = $container->get("stadline_sugar_crm_client");
        $soapMock = $container->get("stadline_front.soap_service");

        $opportunity1 = new Opportunity();
        $opportunity1->setId('1');
        $opportunity1->setName('Test Lundi Matin');
        $opportunity1->setCreatedAt(new \DateTime());
        $opportunity1->setClosedAt(new \DateTime());
        $opportunity1->setAssignedUserName('UsernameTest');
        $opportunity1->setOpportunityType('test');
        $opportunity1->setCampaignName('test');
        $opportunity1->setLeadSource('test');
        $opportunity1->setAmount(100);
        $opportunity1->setSalesStage('facture');
        $opportunity1->setTypeAffaireC('');
        $opportunity1->setidLMB('C-000000-00001');
        $opportunity1->setnumfact('BLC-00004');
        $sugarMock->addOpportunity($opportunity1);

        $manager = $container->get("stadline_task.assign_facture");

        $output = $this->getOutputMock();
        $output->shouldReceive('writeln')->with('> Affaire avec numéro LM trouvée pour Test Lundi Matin : Test Lundi Matin')->once()->andReturn(true);
        $output->shouldReceive('writeln')->with('> Test Lundi Matin : aucune erreur - mise à jour cas 16 effectuée')->once()->andReturn(true);
        $manager->executeStructurePhaseCommand($output);
    }

    public function testCase17(){
        $client = static::createClient();

        $container = $client->getContainer();
        $sugarMock = $container->get("stadline_sugar_crm_client");
        $soapMock = $container->get("stadline_front.soap_service");

        $opportunity1 = new Opportunity();
        $opportunity1->setId('1');
        $opportunity1->setName('Test Lundi Matin');
        $opportunity1->setCreatedAt(new \DateTime());
        $opportunity1->setClosedAt(new \DateTime());
        $opportunity1->setAssignedUserName('UsernameTest');
        $opportunity1->setOpportunityType('test');
        $opportunity1->setCampaignName('test');
        $opportunity1->setLeadSource('test');
        $opportunity1->setAmount(100);
        $opportunity1->setSalesStage('facture');
        $opportunity1->setTypeAffaireC('');
        $opportunity1->setidLMB('C-000000-00002');
        $opportunity1->setnumfact('BLC-00150');
        $sugarMock->addOpportunity($opportunity1);

        $manager = $container->get("stadline_task.assign_facture");

        $output = $this->getOutputMock();
        $output->shouldReceive('writeln')->with('> Affaire avec numéro LM trouvée pour Test Lundi Matin : Test Lundi Matin')->once()->andReturn(true);
        $output->shouldReceive('writeln')->with('> Test Lundi Matin : aucune erreur - mise à jour cas 17 effectuée')->once()->andReturn(true);
        $manager->executeStructurePhaseCommand($output);
    }

    public function testCase18(){
        $client = static::createClient();

        $container = $client->getContainer();
        $sugarMock = $container->get("stadline_sugar_crm_client");
        $soapMock = $container->get("stadline_front.soap_service");

        $opportunity1 = new Opportunity();
        $opportunity1->setId('1');
        $opportunity1->setName('Test Lundi Matin');
        $opportunity1->setCreatedAt(new \DateTime());
        $opportunity1->setClosedAt(new \DateTime());
        $opportunity1->setAssignedUserName('UsernameTest');
        $opportunity1->setOpportunityType('test');
        $opportunity1->setCampaignName('test');
        $opportunity1->setLeadSource('test');
        $opportunity1->setAmount(100);
        $opportunity1->setSalesStage('a_facture');
        $opportunity1->setTypeAffaireC('');
        $opportunity1->setidLMB('C-000000-00004');
        $opportunity1->setnumfact('BLC-00170');
        $sugarMock->addOpportunity($opportunity1);

        $manager = $container->get("stadline_task.assign_facture");

        $output = $this->getOutputMock();
        $output->shouldReceive('writeln')->with('> Affaire avec numéro LM trouvée pour Test Lundi Matin : Test Lundi Matin')->once()->andReturn(true);
        $output->shouldReceive('writeln')->with('> Test Lundi Matin : aucune erreur - mise à jour cas 18 effectuée')->once()->andReturn(true);
        $manager->executeStructurePhaseCommand($output);
    }

    public function testCase19(){
        $client = static::createClient();

        $container = $client->getContainer();
        $sugarMock = $container->get("stadline_sugar_crm_client");
        $soapMock = $container->get("stadline_front.soap_service");

        $opportunity1 = new Opportunity();
        $opportunity1->setId('1');
        $opportunity1->setName('Test Lundi Matin');
        $opportunity1->setCreatedAt(new \DateTime());
        $opportunity1->setClosedAt(new \DateTime());
        $opportunity1->setAssignedUserName('UsernameTest');
        $opportunity1->setOpportunityType('test');
        $opportunity1->setCampaignName('test');
        $opportunity1->setLeadSource('test');
        $opportunity1->setAmount(100);
        $opportunity1->setSalesStage('a_facture');
        $opportunity1->setTypeAffaireC('');
        $opportunity1->setidLMB('C-000000-00005');
        $opportunity1->setnumfact('BLC-00180');
        $sugarMock->addOpportunity($opportunity1);

        $manager = $container->get("stadline_task.assign_facture");

        $output = $this->getOutputMock();
        $output->shouldReceive('writeln')->with('> Affaire avec numéro LM trouvée pour Test Lundi Matin : Test Lundi Matin')->once()->andReturn(true);
        $output->shouldReceive('writeln')->with('> Test Lundi Matin : aucune erreur - mise à jour cas 19 effectuée')->once()->andReturn(true);
        $manager->executeStructurePhaseCommand($output);
    }

    public function getOutputMock()
    {
        $output = m::mock("Symfony\Component\Console\Output\Output");
        return $output;
    }
}
