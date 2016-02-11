<?php
/**
 * Created by PhpStorm.
 * User: gaetan
 * Date: 1/15/16
 * Time: 2:28 PM
 */

namespace Stadline\TasksBundle\Tests\Manager;

use Mockery as m;
use Stadline\SugarCRMBundle\Service\GetOpportunities\Opportunity;
use Stadline\TasksBundle\Manager\InvoiceHandlerManager;
use Stadline\TasksBundle\Service\ManagerCommandService;
use Stadline\TasksBundle\Test\WebTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class AssignFactureCommandTest extends WebTestCase {

    public function testAssignFactureOk(){
        $client = static::createClient();

        $container = $client->getContainer();
        $sugarMock = $container->get("stadline_sugar_crm_client");
        $soapMock = $container->get("stadline_front.soap_service");

        $opportunity1 = new Opportunity();
        $opportunity1->setId('1');
        $opportunity1->setName('Test same montant 1');
        $opportunity1->setCreatedAt(new \DateTime());
        $opportunity1->setClosedAt(new \DateTime());
        $opportunity1->setAssignedUserName('UsernameTest');
        $opportunity1->setOpportunityType('test');
        $opportunity1->setCampaignName('test');
        $opportunity1->setLeadSource('test');
        $opportunity1->setAmount(100);
        $opportunity1->setSalesStage('facture');
        $opportunity1->setTypeAffaireC('');
        $opportunity1->setidLMB('C-000000-00005');
        $opportunity1->setnumfact('');
        $sugarMock->addOpportunity($opportunity1);

        $manager = $container->get("stadline_task.assign_facture");

        $output = $this->getOutputMock();
        $output->shouldReceive('writeln')->with('> Facture inconnue sur Sugar trouvée : BLC-00180 pour le montant HT de 100')->once()->andReturn(true);
        $output->shouldReceive('writeln')->with('> Affaire sans numéro LM trouvée pour Test Lundi Matin : Test same montant 1')->once()->andReturn(true);
        $output->shouldReceive('writeln')->with('> Test same montant 1 : aucune erreur - mise à jour effectuée')->once()->andReturn(true);
        $manager->executeAssignFactureCommand($output);
    }

    public function testSomeFactureWithSameAmount()
    {
        $client = static::createClient();

        $container = $client->getContainer();
        $sugarMock = $container->get("stadline_sugar_crm_client");
        $soapMock = $container->get("stadline_front.soap_service");

        $opportunity1 = new Opportunity();
        $opportunity1->setId('1');
        $opportunity1->setName('Test same montant 1');
        $opportunity1->setCreatedAt(new \DateTime());
        $opportunity1->setAssignedUserName('UsernameTest');
        $opportunity1->setOpportunityType('test');
        $opportunity1->setCampaignName('test');
        $opportunity1->setLeadSource('test');
        $opportunity1->setAmount(100);
        $opportunity1->setSalesStage('facture');
        $opportunity1->setTypeAffaireC('');
        $opportunity1->setidLMB('C-000000-00003');
        $opportunity1->setnumfact('');
        $sugarMock->addOpportunity($opportunity1);

        $manager = $container->get("stadline_task.assign_facture");

        $output = $this->getOutputMock();
        $output->shouldReceive('writeln')->with('> Facture inconnue sur Sugar trouvée : BLC-00150 pour le montant HT de 100')->once()->andReturn(true);
        $output->shouldReceive('writeln')->with('> Facture inconnue sur Sugar trouvée : BLC-00160 pour le montant HT de 100')->once()->andReturn(true);
        $output->shouldReceive('writeln')->with('> Affaire sans numéro LM trouvée pour Test Lundi Matin : Test same montant 1')->once()->andReturn(true);
        $output->shouldReceive('writeln')->with("> Test same montant 1 : il existe plusieurs factures avec le même montant - pas de mise à jour")->once()->andReturn(true);
        $manager->executeAssignFactureCommand($output);
    }

    public function testSomeAffairWithSameAmount()
    {
        $client = static::createClient();

        $container = $client->getContainer();
        $sugarMock = $container->get("stadline_sugar_crm_client");
        $soapMock = $container->get("stadline_front.soap_service");

        $opportunity1 = new Opportunity();
        $opportunity1->setId('1');
        $opportunity1->setName('Test same montant 1');
        $opportunity1->setCreatedAt(new \DateTime());
        $opportunity1->setAssignedUserName('UsernameTest');
        $opportunity1->setOpportunityType('test');
        $opportunity1->setCampaignName('test');
        $opportunity1->setLeadSource('test');
        $opportunity1->setAmount(100);
        $opportunity1->setSalesStage('facture');
        $opportunity1->setTypeAffaireC('');
        $opportunity1->setidLMB('C-000000-00002');
        $opportunity1->setnumfact('');
        $sugarMock->addOpportunity($opportunity1);

        $opportunity2 = new Opportunity();
        $opportunity2->setId('2');
        $opportunity2->setName('Test same montant 2');
        $opportunity2->setCreatedAt(new \DateTime());
        $opportunity2->setAssignedUserName('UsernameTest2');
        $opportunity2->setOpportunityType('test2');
        $opportunity2->setCampaignName('test2');
        $opportunity2->setLeadSource('test2');
        $opportunity2->setAmount(100);
        $opportunity2->setSalesStage('facture');
        $opportunity2->setTypeAffaireC('');
        $opportunity2->setidLMB('C-000000-00002');
        $opportunity2->setnumfact('');
        $sugarMock->addOpportunity($opportunity2);

        $manager = $container->get("stadline_task.assign_facture");

        $output = $this->getOutputMock();
        $output->shouldReceive('writeln')->with('> Facture inconnue sur Sugar trouvée : BLC-00150 pour le montant HT de 100')->once()->andReturn(true);
        $output->shouldReceive('writeln')->with('> Affaire sans numéro LM trouvée pour Test Lundi Matin : Test same montant 1')->once()->andReturn(true);
        $output->shouldReceive('writeln')->with('> Affaire sans numéro LM trouvée pour Test Lundi Matin : Test same montant 2')->once()->andReturn(true);
        $output->shouldReceive('writeln')->with('> Affaire sans numéro LM trouvée pour Test Lundi Matin : Test same montant 2')->once()->andReturn(true);
        $output->shouldReceive('writeln')->with("> Test same montant 1 : il existe plusieurs affaires avec le même montant - pas de mise à jour")->once()->andReturn(true);
        $manager->executeAssignFactureCommand($output);
    }

    public function testSomeAffairWithSameAmountAndNoFactureMatch()
    {
        $client = static::createClient();

        $container = $client->getContainer();
        $sugarMock = $container->get("stadline_sugar_crm_client");
        $soapMock = $container->get("stadline_front.soap_service");

        $opportunity1 = new Opportunity();
        $opportunity1->setId('1');
        $opportunity1->setName('Test same montant 1');
        $opportunity1->setCreatedAt(new \DateTime());
        $opportunity1->setAssignedUserName('UsernameTest');
        $opportunity1->setOpportunityType('test');
        $opportunity1->setCampaignName('test');
        $opportunity1->setLeadSource('test');
        $opportunity1->setAmount(100);
        $opportunity1->setSalesStage('facture');
        $opportunity1->setTypeAffaireC('');
        $opportunity1->setidLMB('C-000000-00001');
        $opportunity1->setnumfact('');
        $sugarMock->addOpportunity($opportunity1);

        $opportunity2 = new Opportunity();
        $opportunity2->setId('2');
        $opportunity2->setName('Test same montant 2');
        $opportunity2->setCreatedAt(new \DateTime());
        $opportunity2->setAssignedUserName('UsernameTest2');
        $opportunity2->setOpportunityType('test2');
        $opportunity2->setCampaignName('test2');
        $opportunity2->setLeadSource('test2');
        $opportunity2->setAmount(100);
        $opportunity2->setSalesStage('facture');
        $opportunity2->setTypeAffaireC('');
        $opportunity2->setidLMB('C-000000-00001');
        $opportunity2->setnumfact('');
        $sugarMock->addOpportunity($opportunity2);

        $manager = $container->get("stadline_task.assign_facture");

        $output = $this->getOutputMock();
        $output->shouldReceive('writeln')->with('> Affaire sans numéro LM trouvée pour Test Lundi Matin : Test same montant 1')->once()->andReturn(true);
        $output->shouldReceive('writeln')->with('> Affaire sans numéro LM trouvée pour Test Lundi Matin : Test same montant 2')->once()->andReturn(true);
        $output->shouldReceive('writeln')->with('> Affaire sans numéro LM trouvée pour Test Lundi Matin : Test same montant 2')->once()->andReturn(true);
        $output->shouldReceive('writeln')->with("> Test same montant 1 : il existe plusieurs affaires avec le même montant et aucune facture ne correspond - pas de mise à jour")->once()->andReturn(true);
        $manager->executeAssignFactureCommand($output);
    }

    public function testNoMontantForThisAffair()
    {
        $client = static::createClient();

        $container = $client->getContainer();
        $sugarMock = $container->get("stadline_sugar_crm_client");
        $soapMock = $container->get("stadline_front.soap_service");

        $opportunity1 = new Opportunity();
        $opportunity1->setId('1');
        $opportunity1->setName('Test same montant 1');
        $opportunity1->setCreatedAt(new \DateTime());
        $opportunity1->setAssignedUserName('UsernameTest');
        $opportunity1->setOpportunityType('test');
        $opportunity1->setCampaignName('test');
        $opportunity1->setLeadSource('test');
        $opportunity1->setAmount(100);
        $opportunity1->setSalesStage('facture');
        $opportunity1->setTypeAffaireC('');
        $opportunity1->setidLMB('C-000000-00004');
        $opportunity1->setnumfact('');
        $sugarMock->addOpportunity($opportunity1);

        $manager = $container->get("stadline_task.assign_facture");

        $output = $this->getOutputMock();
        $output->shouldReceive('writeln')->with('> Facture inconnue sur Sugar trouvée : BLC-00170 pour le montant HT de 150')->once()->andReturn(true);
        $output->shouldReceive('writeln')->with('> Affaire sans numéro LM trouvée pour Test Lundi Matin : Test same montant 1')->once()->andReturn(true);
        $output->shouldReceive('writeln')->with("> Test same montant 1 : aucun montant ne correspond à cette affaire - pas de mise à jour")->once()->andReturn(true);
        $manager->executeAssignFactureCommand($output);
    }
    public function getOutputMock()
    {
        $output = m::mock("Symfony\Component\Console\Output\Output");
        return $output;
    }
}
 