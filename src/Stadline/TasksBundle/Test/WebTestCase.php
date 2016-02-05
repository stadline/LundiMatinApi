<?php
/**
 * Created by PhpStorm.
 * User: gaetan
 * Date: 1/15/16
 * Time: 9:48 AM
 */

namespace Stadline\TasksBundle\Test;

use Liip\FunctionalTestBundle\Test\WebTestCase as BaseWebTestCase;

class WebTestCase extends BaseWebTestCase
{
    /**
     * @{inheritDoc}
     *
     * @return \Symfony\Component\DependencyInjection\Container
     */
    public function getContainer()
    {
        return parent::getContainer();
    }

    /**
     * @{inheritDoc}
     */
    protected function loadFixtures($fixtures = null)
    {
        if (is_null($fixtures)) {
            // browse bundles to get all possible paths
            $kernel = $this->getContainer()->get('kernel');

            $paths = array();
            foreach ($kernel->getBundles() as $bundle) {
                $paths[] = $bundle->getPath().'/DataFixtures/ORM';
            }

            // browse paths to get all possible fixture files
            $includedFiles = array();

            foreach ($paths as $dir) {
                if (!is_dir($dir)) {
                    continue;
                }

                $iterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($dir),
                    \RecursiveIteratorIterator::LEAVES_ONLY
                );

                foreach ($iterator as $file) {
                    if (($fileName = $file->getBasename('.php')) == $file->getBasename()) {
                        continue;
                    }
                    $sourceFile = realpath($file->getPathName());
                    require_once $sourceFile;
                    $includedFiles[] = $sourceFile;
                }
            }

            // browse files and check if it is a fixture
            $declared = get_declared_classes();
            $fixtures = array();

            foreach ($declared as $className) {
                $reflClass = new \ReflectionClass($className);
                $sourceFile = $reflClass->getFileName();

                if (in_array($sourceFile, $includedFiles) && ! $this->isTransient($className)) {
                    $fixtures[] = $className;
                }
            }
        }

        parent::loadFixtures($fixtures);
    }

    /**
     * Check if a given fixture is transient and should not be considered a data fixtures
     * class.
     *
     * @return boolean
     */
    public function isTransient($className)
    {
        $rc = new \ReflectionClass($className);
        if ($rc->isAbstract()) return true;

        $interfaces = class_implements($className);
        return in_array('Doctrine\Common\DataFixtures\FixtureInterface', $interfaces) ? false : true;
    }


    protected function assertJsonResponse($response, $statusCode = 200)
    {
        $this->assertEquals(
            $statusCode, $response->getStatusCode(),
            $response->getContent()
        );
        $this->assertTrue(
            $response->headers->contains('Content-Type', 'application/json+hal'),
            $response->headers
        );
    }

    protected function jsonRequest($verb, $endpoint, array $data = array())
    {
        $data = empty($data) ? null : json_encode($data);

        return $this->client->request($verb, $endpoint,
            array(),
            array(),
            array(
                'HTTP_ACCEPT'  => 'application/json',
                'CONTENT_TYPE' => 'application/json'
            ),
            $data
        );
    }
}
