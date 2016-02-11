<?php
/**
 * Created by PhpStorm.
 * User: gaetan
 * Date: 2/5/16
 * Time: 11:31 AM
 */

namespace Stadline\TasksBundle\DependencyInjection\CompilerPass;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class AssignCompilerPass implements CompilerPassInterface{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        if(!$container->has('stadline_task.assign_facture')){
            return;
        }

        $definition = $container->findDefinition(
            'stadline_task.assign_facture'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'stadline_task.filter'
        );

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall(
                'addAssignFilter',
                array(new Reference($id))
            );
        }
    }
}