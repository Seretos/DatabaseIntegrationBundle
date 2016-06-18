<?php
/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 18.06.2016
 * Time: 05:53
 */

namespace database\DatabaseIntegrationBundle\factory;


use database\DriverBundle\factory\DriverBundleFactory;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\PDOConnection;

class DatabaseIntegrationBundleFactory {
    public function convertDoctrineConnection (DriverBundleFactory $driverFactory, Connection $connection) {
        /* @var $pdo PDOConnection */
        $pdo = $connection->getWrappedConnection();

        return $driverFactory->convertPdo($pdo);
    }

    public function createConnection (DriverBundleFactory $driverFactory,
                                      $host,
                                      $user,
                                      $password,
                                      $database,
                                      $port = 3306) {
        return $driverFactory->createPdoConnection($host, $user, $password, $database, $port);
    }
}