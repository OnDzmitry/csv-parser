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

class ImportService
{
    private $em;
    private $container;
    private $skipped = 0;
    private $successful = 0;
    private $processed = 0;
    private $skippedItems = [];

    public function __construct($container)
    {
        $this->container = $container;
        $this->em = $container->get('doctrine.orm.entity_manager');
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

    private function import(array $products, $mode)
    {
        $mode->import($products);
    }

    public function getSkippedItems()
    {
        return $this->skippedItems;
    }

    public function getSkipped(): int
    {
        return $this->skipped;
    }

    public function getSuccessful(): int
    {
        return $this->successful;
    }

    public function getProcessed(): int
    {
        return $this->processed;
    }
}