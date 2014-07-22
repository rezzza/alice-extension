<?php

namespace Rezzza\AliceExtension\Adapter\Elastica;

use FOS\ElasticaBundle\Elastica\Index;
use JMS\Serializer\SerializerInterface;

class Persister
{
    private $index;

    private $mapping;

    private $serializer;

    public function __construct(Index $index, array $mapping, SerializerInterface $serializer)
    {
        $this->index = $index;
        $this->mapping = $mapping;
        $this->serializer = $serializer;
    }

    public function persist(array $data)
    {
        foreach ($data as $value) {
            $json = $this->serializer->serialize($value, 'json');
            $typeName = array_search(get_class($value), $this->mapping);
            $type = $this->index->getType($typeName);
            $type->addDocument(new \Elastica\Document('', $json));
        }

        $this->index->refresh(); // Force to refresh, to be sure we will make test requests on fresh data !
    }
}
