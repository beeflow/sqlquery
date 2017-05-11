<?php
/**
 * @author   Rafal Przetakowski <rafal.p@beeflow.co.uk>
 * @copyright: (c) 2017 Beeflow Ltd
 *
 * Date: 08.04.17 15:49
 */

namespace Beeflow\SQLQueryManager;

use Beeflow\SQLQueryManager\DependencyInjection\Compiler\SQLQueryVartypePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SQLQueryBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new SQLQueryVartypePass());
    }
}
