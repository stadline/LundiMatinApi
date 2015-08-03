<?php

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

$collection->add('stadline_tasks_homepage', new Route('/hello/{name}', array(
    '_controller' => 'StadlineTasksBundle:Default:index',
$collection->add('PageLog',new Route('/log',array('controller'=>'')))
)));

return $collection;
