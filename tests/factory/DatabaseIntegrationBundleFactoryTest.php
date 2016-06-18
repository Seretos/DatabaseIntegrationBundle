<?php
use database\DatabaseIntegrationBundle\factory\DatabaseIntegrationBundleFactory;
use database\DriverBundle\factory\DriverBundleFactory;
use Doctrine\DBAL\Connection;

/**
 * Created by PhpStorm.
 * User: Seredos
 * Date: 18.06.2016
 * Time: 22:44
 */
class DatabaseIntegrationBundleFactoryTest extends PHPUnit_Framework_TestCase {
    /**
     * @var DatabaseIntegrationBundleFactory
     */
    private $factory;

    protected function setUp () {
        $this->factory = new DatabaseIntegrationBundleFactory();
    }

    /**
     * @test
     */
    public function convertDoctrineConnection () {
        /**
         * @var $mockDriverFactory DriverBundleFactory|PHPUnit_Framework_MockObject_MockObject
         * @var $mockConnection    Connection|PHPUnit_Framework_MockObject_MockObject
         */
        $mockDriverFactory = $this->getMockBuilder(DriverBundleFactory::class)
                                  ->disableOriginalConstructor()
                                  ->getMock();
        $mockConnection = $this->getMockBuilder(Connection::class)
                               ->disableOriginalConstructor()
                               ->getMock();

        $mockPdo = $this->getMockBuilder(PDO::class)
                        ->disableOriginalConstructor()
                        ->getMock();

        $mockConnection->expects($this->once())
                       ->method('getWrappedConnection')
                       ->will($this->returnValue($mockPdo));

        $mockDriverFactory->expects($this->once())
                          ->method('convertPdo')
                          ->with($mockPdo)
                          ->will($this->returnValue('success'));

        $this->assertSame('success', $this->factory->convertDoctrineConnection($mockDriverFactory, $mockConnection));
    }

    /**
     * @test
     */
    public function createConnection () {
        /**
         * @var $mockDriverFactory DriverBundleFactory|PHPUnit_Framework_MockObject_MockObject
         */
        $mockDriverFactory = $this->getMockBuilder(DriverBundleFactory::class)
                                  ->disableOriginalConstructor()
                                  ->getMock();

        $mockDriverFactory->expects($this->once())
                          ->method('createPdoConnection')
                          ->with('test1', 'test2', 'test3', 'test4', 10)
                          ->will($this->returnValue('success'));

        $this->assertSame('success',
                          $this->factory->createConnection($mockDriverFactory, 'test1', 'test2', 'test3', 'test4', 10));
    }
}