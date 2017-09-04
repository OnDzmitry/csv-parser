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
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\VarDumper\Cloner\Data;

class ImportService
{
    protected $em;
    protected $container;
    protected $reader;
    protected $mode;

    public function __construct($container)
    {
        $this->container = $container;
        $this->em = $container->get('doctrine.orm.entity_manager');
    }

    public function handle(string $filePath, Mode $mode)
    {
        $this->mode = $mode;
        $reader = Reader::createFromPath($filePath);
        $reader->setHeaderOffset(0);
        $records = (new Statement())->process($reader);
        $headers = $records->getHeader();
        $products = [];

        foreach ($records as $record) {
            $product = new Product();
            $product->setCode($record[$headers[0]]);
            $product->setName($record[$headers[1]]);
            $product->setDesc($record[$headers[2]]);
            $product->setStock($record[$headers[3]]);
            $product->setCost($record[$headers[4]]);
            $product->setDiscontinued($record[$headers[5]]);
            //$product->setAddAt(new \DateTime());
            array_push($products, $product);
        }
        $str = $this->em->getClassMetadata(Product::class)->getFieldNames();
        foreach ($products as $product) {
            $errors = $this->container->get('validator')->validate($product);
        }
        $this->import($products);
    }

    private function import(array $products) : void
    {
        $this->mode->import($products);
    }
}