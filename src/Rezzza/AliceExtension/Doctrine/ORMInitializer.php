<?php

namespace Rezzza\AliceExtension\Doctrine;

use Behat\Symfony2Extension\Context\KernelAwareInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ORMInitializer
{
    private $em;

    private $migrationConfig;

    private $database;

    public function __construct(ObjectManager $em, $database, array $migrationConfig)
    {
        $this->em = $em;
        $this->database = $database;
        $this->migrationConfig = $migrationConfig;
    }

    public function initDatabase()
    {
        $connection = $this->em->getConnection();

        $connection
            ->getSchemaManager()
            ->dropAndCreateDatabase($this->database)
        ;
        $connection->close();

        $configuration = new \Doctrine\DBAL\Migrations\Configuration\Configuration($connection);
        $configuration->setMigrationsDirectory($this->migrationConfig['dir_name']);
        $configuration->setMigrationsNamespace($this->migrationConfig['namespace']);
        $configuration->setMigrationsTableName($this->migrationConfig['table_name']);
        $configuration->createMigrationTable();

        $configuration->registerMigrationsFromDirectory($this->migrationConfig['dir_name']);

        $migration = new \Doctrine\DBAL\Migrations\Migration($configuration);
        $migration->migrate();
        $connection->close();
    }
}
