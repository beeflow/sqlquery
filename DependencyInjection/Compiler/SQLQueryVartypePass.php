<?php
/**
 * @author        Rafal Przetakowski <rafal.p@beeflow.co.uk>
 * @copyright (c) Beeflow Ltd
 */

namespace Beeflow\SQLQueryManager\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class SQLQueryVartypePass implements CompilerPassInterface
{
    /**
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('beeflow.sql_query_manager')) {
            return;
        }

        $definition = $container->findDefinition('beeflow.sql_query_manager');
        $taggedServices = $container->findTaggedServiceIds('beeflow.sql_manager.vartype');

        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $definition->addMethodCall('addVarType', [
                    new Reference($id),
                    $attributes['alias']
                ]);
            }
        }
    }
}
