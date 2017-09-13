<?php
/**
 * Created by PhpStorm.
 * User: d2.kozlovsky
 * Date: 4.9.17
 * Time: 16.11
 */

namespace AppBundle\Services;

use AppBundle\Models\ImportMods\Mode;
use AppBundle\Services\Parsers\Parser;
use AppBundle\Services\Validators\Validator;
use Doctrine\ORM\EntityManager;

class ImportService

{
    private $em;
    private $skipped = 0;
    private $successful = 0;
    private $processed = 0;
    private $skippedItems = [];
    private $parser;
    private $validator;

    public function __construct(EntityManager $em, Parser $parser, Validator $validator)
    {
        $this->em = $em;
        $this->parser = $parser;
        $this->validator = $validator;
    }

    public function handle(Mode $mode)
    {
        $parser = $this->parser;
        $validator = $this->validator;

        $parser->parse();
        $items = $parser->getItems();
        $this->processed = $parser->getProcessedCount();

        $validator->validate($items);

        $this->successful = $validator->getSuccessfulCount();
        $this->skipped = $validator->getSkippedCount();
        $this->skippedItems = $validator->getSkippedItems();

        $successfulItems = $validator->getSuccessfulItems();

        $this->import($successfulItems, $mode);
    }

    /**
     * @param array $products
     * @param $mode
     */
    private function import(array $products, $mode)
    {
        $mode->import($products);
    }

    /**
     * @return array
     */
    public function getSkippedItems()
    {
        return $this->skippedItems;
    }

    /**
     * @return int
     */
    public function getSkipped(): int
    {
        return $this->skipped;
    }

    /**
     * @return int
     */
    public function getSuccessful(): int
    {
        return $this->successful;
    }

    /**
     * @return int
     */
    public function getProcessed(): int
    {
        return $this->processed;
    }
}