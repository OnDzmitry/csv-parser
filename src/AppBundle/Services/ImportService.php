<?php
/**
 * Created by PhpStorm.
 * User: d2.kozlovsky
 * Date: 4.9.17
 * Time: 16.11
 */

namespace AppBundle\Services;

use AppBundle\Models\ImportMods\Mode;
use AppBundle\Models\Parsers\Parser;
use AppBundle\Models\Validators\Validator;
use Doctrine\ORM\EntityManager;

class ImportService
{
    private $em;
    private $skipped = 0;
    private $successful = 0;
    private $processed = 0;
    private $skippedItems = [];

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function handle(Mode $mode, Parser $parser, Validator $validator)
    {
        $parser->parse();
        $items = $parser->getItems();
        $this->processed = $parser->getProcessedCount();

        $validator->validate($items);
        $this->successful = $validator->getSuccessfulCount();
        $this->skipped = $validator->getSkippedCount();

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