DatabaseIntegrationBundle
=========================
this bundle combines the mysqli database bundles with the symfony dependency component.

Installation
============
add the bundle in your composer.json as bellow:
```js
"require": {
    ...
    ,"LimetecBiotechnologies/database/DatabaseIntegrationBundle" : "v0.1.*"
},
  "repositories": [
    {
      "type": "git",
      "url": "https://github.com/Seretos/DriverBundle"
    },
    {
      "type": "git",
      "url": "https://github.com/Seretos/QueryBundle"
    },
    {
      "type": "git",
      "url": "https://github.com/Seretos/QueryBuilderBundle"
    },
    {
      "type" : "git",
      "url" : "https://github.com/Seretos/DatabaseIntegrationBundle"
    }
]
```
and execute the composer update command

Usage
=====
```php
    /* 
     * @var $queryFactory        database\QueryBundle\factory\QueryBundleFactory
     * @var $queryBuilderFactory database\QueryBuilderBundle\factory\QueryBuilderBundleFactory
     */
    $queryFactory = $container->get('database.connection.default.query.factory');
    $queryBuilderFactory = $container->get('database.querybuilder.factory');
        
    $builder = $queryBuilderFactory->createQueryBuilder();
        
    $builder->select('*')
            ->from('example1','e1');
        
    $query = $queryFactory->createQuery($builder,$builder->getParameters());
    $resultIterator = $query->buildResult();
```

now the following dependency-services are configured in your application:
```php
    $driverFactory = $container->get('database.driver.factory');
    $integrationFactory = $container->get('database.integration.factory');
    $builderFactory = $container->get('database.querybuilder.factory');
```
in symfony, the following dependency-services are automaticly generated for every doctrine connection (defined in the config.yml)
```php
    $connection = $container->get('database.connection.yourConnectionName');
    $queryFactory = $container->get('database.connection.yourConnectionName.query.factory');
```

configuration with Symfony
==========================
register this bundle in your AppKernel.php as below:
```php
    public function registerBundles()
    {
        $bundles = [
            ...
            new database\DatabaseIntegrationBundle\DatabaseIntegrationBundle(),
        ];
        ...
    }        
```

configuration without Symfony
=============================
you can modify your DatabaseIntegrationBundleFactory to customize the connection creation (or convertion):
```php
   class DatabaseIntegrationBundleFactory {
        ...
        public function convertMyConnection(DriverBundleFactory $driverFactory, $connectionName){
            $connection = MyConnection::getConnection($connectionName);
            return $driverFactory->convertMysqli($connection);
        }
   }
```

register the following services in your dependency config:
```php
services:
     ...
     database.driver.factory:
         class: database\DriverBundle\factory\DriverBundleFactory
     database.querybuilder.factory:
         class: database\QueryBuilderBundle\factory\QueryBuilderBundleFactory
     database.integration.factory:
         class: database\DatabaseIntegrationBundle\factory\DatabaseIntegrationBundleFactory
```

create the services for your connection(s):
```php
services:
     ...
     database.connection.default:
         class: database\DriverBundle\connection\interfaces\ConnectionInterface
         factory: ['database.integration.factory',createConnection]
         arguments: ['database.driver.factory','127.0.0.1','user','pwd','database',3306]
     
     database.connection.default.query.factory
         class: database\QueryBundle\factory\QueryBundleFactory
         arguments: ['database.connection.default']
     
     #import the default connection from your custom function
     database.connection.import:
         class: database\DriverBundle\connection\interfaces\ConnectionInterface
         factory: ['database.integration.factory',convertMyConnection]
         arguments: ['database.driver.factory','default']
```