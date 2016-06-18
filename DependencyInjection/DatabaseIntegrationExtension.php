<?php

namespace database\DatabaseIntegrationBundle\DependencyInjection;

use database\DatabaseIntegrationBundle\factory\DatabaseIntegrationBundleFactory;
use database\DriverBundle\connection\interfaces\ConnectionInterface;
use database\QueryBundle\factory\QueryBundleFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class DatabaseIntegrationExtension extends Extension {
    /**
     * {@inheritdoc}
     */
    public function load (array $configs, ContainerBuilder $container) {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $connections = $container->getParameter('doctrine.connections');
        foreach ($connections as $name => $connection) {
            $connectionId = sprintf('database.connection.%s', $name);
            $connectionQueryId = sprintf('database.connection.%s.query.factory', $name);

            $definition = new Definition(ConnectionInterface::class);
            $definition->setFactory([DatabaseIntegrationBundleFactory::class, 'convertDoctrineConnection']);
            $definition->setArguments([new Reference('database.driver.factory'), new Reference($connection)]);
            $container->setDefinition($connectionId, $definition);

            $definition = new Definition(QueryBundleFactory::class);
            $definition->setArguments([new Reference($connectionId)]);
            $container->setDefinition($connectionQueryId, $definition);
        }
    }
}
