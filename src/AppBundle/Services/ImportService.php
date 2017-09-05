<?php
/**
 * Created by PhpStorm.
 * User: d2.kozlovsky
 * Date: 4.9.17
 * Time: 16.11
 */

namespace AppBundle\Services;
use AppBundle\Entity\Product;
use League\Csv\Reader;
use League\Csv\Statement;
use AppBundle\ImportMods\Mode;

class ImportService
{
    private $em;
    private $container;
    private $mode;
    private $skipped = 0;
    private $successful = 0;
    private $failProducts = [];

    public function __construct($container)
    {
        $this->container = $container;
        $this->em = $container->get('doctrine.orm.entity_manager');
    }

    public function handle(string $filePath, Mode $mode)
    {
        $this->mode = $mode;
        $products = $this->parseProducts($filePath);
        $this->import($products);
    }

    private function parseProducts($filePath) : array
    {
        $reader = Reader::createFromPath($filePath);
        $reader->setHeaderOffset(0);
        $records = (new Statement())->process($reader);
        $headers = $records->getHeader();
        $products = [];
        $errors = [];
        foreach ($records as $record) {
            $repository = $this->container->get('doctrine')->getRepository(Product::class);
            $product = $repository->findOneByCode($record[$headers[0]]);
            if (!isset($product)) {
                $product = new Product();
            }
            $mapping = $this->container->getParameter('mapping');

            $product->setCode($record[$headers[0]]);
            $product->setName($record[$headers[1]]);
            $product->setDesc($record[$headers[2]]);
            $product->setStock($record[$headers[3]]);
            $product->setCost($record[$headers[4]]);
            $product->setDiscontinued($record[$headers[5]]);

            $product->setAddAt(new \DateTime("now"));
            $product->setTimestamp(new \DateTime("now"));

            $errors = $this->validateProduct($product);

            if (count($errors) >= 1) {
                array_push($this->failProducts, ['product' => $product, 'errors' => $errors]);
                $this->skipped++;
            } else {
                array_push($products, $product);
                $this->successful++;
            }
        }
        return $products;
    }

    private function validateProduct($product)
    {
        $validator = $this->container->get('validator');
        $errors = $validator->validate($product);
        return $errors;
    }

    public function getResultMessage() : string
    {
        return 'Process successful!' .
            'Processed ' . (int)($this->skipped + $this->successful) . "\n" .
            'Successful ' . $this->successful . "\n" .
            'Skipped ' . $this->skipped . "\n";
    }

    public function getFailProductsMessage() : string
    {
        $resutlStr = "Items which fail to be inserted correctly:\n";
        foreach ($this->failProducts as $record) {
            $resutlStr .= $record['product'] . "\n";
            foreach ($record['errors'] as $error) {
                $resutlStr .= '***' . $error->getMessage() . "\n";
            }
        }
        return $resutlStr;
    }

    private function import(array $products)
    {
        $this->mode->import($products);
    }
}