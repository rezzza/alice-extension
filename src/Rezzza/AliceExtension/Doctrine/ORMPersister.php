<?php

namespace Rezzza\AliceExtension\Doctrine;

use Doctrine\Common\Persistence\ManagerRegistry;
use Nelmio\Alice\ORM\Doctrine;

class ORMPersister extends Doctrine
{
    protected $regristry;
    protected $flush;

    public function __construct(ManagerRegistry $registry, $doFlush = true)
    {
        $this->registry = $registry;
        $this->flush    = $doFlush;
    }

    /**
     * {@inheritDoc}
     */
    public function persist(array $objects)
    {
        $managersToFlush = [];

        foreach ($objects as $object) {
            $managerName = $this->findManagerForClass(get_class($object));
            $this->registry->getManager($managerName)->persist($object);

            $managersToFlush[$managerName] = $managerName;
        }

        if ($this->flush) {
            foreach ($managersToFlush as $managerName) {
                $this->registry->getManager($managerName)->flush();
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function find($class, $id)
    {
        $managerName = $this->findManagerForClass($class);
        $entity      = $this->registry->getManager($managerName)->find($class, $id);

        if (!$entity) {
            throw new \UnexpectedValueException('Entity with Id ' . $id . ' and Class ' . $class . ' not found');
        }

        return $entity;
    }

    private function findManagerForClass($entityClass)
    {
        foreach ($this->registry->getManagers() as $name => $manager) {
            if ($manager->getMetadataFactory()->hasMetadataFor($entityClass)) {
                return $name;
            }
        }

        throw new \Doctrine\Common\Persistence\Mapping\MappingException(sprintf('Entity class "%s" not found in managers.', $entityClass));
    }
}
