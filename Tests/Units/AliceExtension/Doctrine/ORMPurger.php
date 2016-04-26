<?php

namespace Rezzza\AliceExtension\Doctrine\Tests\Units;

use mageekguy\atoum;
use Rezzza\AliceExtension\Doctrine\ORMPurger as TestedClass;

class ORMPurger extends atoum\test
{
    public function it_should_not_truncate_an_embedded_entity()
    {
        $this->given(
            $metadataMock1 = $this->mockClassMetadataInfo(false, 'realTable'),
            $metadataMock2 = $this->mockClassMetadataInfo(true, 'embededTable'),

            $doctrineConnectionMock = $this->mockDoctrineConnection(),

            $classMetadataFactoryMock = $this->mockClassMetadataFactory(array($metadataMock1, $metadataMock2)),

            $managerMock = $this->mockEntityManager($classMetadataFactoryMock, $doctrineConnectionMock),
            $managerRegistryMock = $this->mockManagerRegistry($managerMock),

            $testedClass = new TestedClass($managerRegistryMock)
        )->when(
            $testedClass->purge()
        )->then(
            $this
                ->mock($doctrineConnectionMock)
                    ->call('executeUpdate')
                        ->once()
        )
        ;
    }

    /**
     * @return \Doctrine\DBAL\Connection
     */
    private function mockDoctrineConnection()
    {
        $doctrineMySqlPlatformMock = new \mock\Doctrine\DBAL\Platforms\MySqlPlatform;

        $this->mockGenerator()->orphanize('__construct');
        $doctrineConnectionMock = new \mock\Doctrine\DBAL\Connection;
        $doctrineConnectionMock->getMockController()->getDatabasePlatform = $doctrineMySqlPlatformMock;
        $doctrineConnectionMock->getMockController()->exec = null;
        $doctrineConnectionMock->getMockController()->executeUpdate = null;

        return $doctrineConnectionMock;
    }

    /**
     * @param $classMetadataFactoryMock
     * @param $doctrineConnectionMock
     * @return \Doctrine\ORM\EntityManager
     */
    private function mockEntityManager($classMetadataFactoryMock, $doctrineConnectionMock)
    {
        $this->mockGenerator()->orphanize('__construct');
        $managerMock = new \mock\Doctrine\ORM\EntityManager;
        $managerMock->getMockController()->getMetadataFactory = $classMetadataFactoryMock;
        $managerMock->getMockController()->getConnection = $doctrineConnectionMock;

        return $managerMock;
    }

    /**
     * @param \Doctrine\ORM\EntityManager $managerMock
     * @return \Doctrine\Common\Persistence\ManagerRegistry
     */
    private function mockManagerRegistry($managerMock)
    {
        $managerRegistryMock = new \mock\Doctrine\Common\Persistence\ManagerRegistry;
        $managerRegistryMock->getMockController()->getManagers = array($managerMock);

        return $managerRegistryMock;
    }

    /**
     * @param \Doctrine\Common\Persistence\Mapping\ClassMetadata[] $metadata
     * @return \Doctrine\ORM\Mapping\ClassMetadataFactory
     */
    private function mockClassMetadataFactory(array $metadata)
    {
        $classMetadataFactoryMock = new \mock\Doctrine\ORM\Mapping\ClassMetadataFactory;
        $classMetadataFactoryMock->getMockController()->getAllMetadata = $metadata;

        return $classMetadataFactoryMock;
    }

    /**
     * @return \Doctrine\ORM\Mapping\ClassMetadataInfo
     */
    private function mockClassMetadataInfo($isEmbeddedClass, $tableName)
    {
        $this->mockGenerator()->orphanize('__construct');
        $metadataMock = new \mock\Doctrine\ORM\Mapping\ClassMetadataInfo;
        $metadataMock->isEmbeddedClass = $isEmbeddedClass;
        $metadataMock->getMockController()->getQuotedTableName = $tableName;

        return $metadataMock;
    }
}
