<?php
/**
 * Created by PhpStorm.
 * User: d2.kozlovsky
 * Date: 6.9.17
 * Time: 15.57
 */

namespace AppBundle\Models\Parsers;

use League\Csv\Reader;
use League\Csv\Statement;
use Symfony\Component\PropertyAccess\PropertyAccess;

class CsvParser implements Parser
{
    private $filePath;
    private $reader;
    private $records;
    private $entity;
    private $processed = 0;
    private $items = [];
    private $mapping;

    public function __construct($filePath, $mapping, $entity)
    {
        $this->filePath = $filePath;
        $this->reader = Reader::createFromPath($filePath);
        $this->reader->setHeaderOffset(0);
        $this->records = (new Statement())->process($this->reader);
        $this->entity = $entity;
        $this->mapping = $mapping;
    }

    public function parse()
    {
        foreach ($this->records as $record) {
            $item = $this->getItemObject($record);
            array_push($this->items, $item);
            ++$this->processed;
        }
    }

    public function getProcessedCount() : int
    {
        return $this->processed;
    }

    public function getItems() : array
    {
        return $this->items;
    }

    private function getItemObject($record)
    {
        $item = new $this->entity();
        $accessor = PropertyAccess::createPropertyAccessorBuilder()
            ->enableMagicCall()
            ->getPropertyAccessor();
        foreach ($this->mapping as $fileHeaders => $objectProperty) {
            $accessor->setValue($item, $objectProperty, $record[$fileHeaders]);
        }
        return $item;
    }
}