<?php
/**
 * Created by PhpStorm.
 * User: d2.kozlovsky
 * Date: 6.9.17
 * Time: 20.06
 */

namespace Tests\AppBundle\Services;


use AppBundle\Entity\Product;
use AppBundle\Models\ImportMods\TestMode;
use AppBundle\Models\Parsers\CsvParser;
use AppBundle\Models\Validators\ProductValidator;
use AppBundle\Services\ImportService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ImportServiceTest extends WebTestCase
{
    private $importService;
    private $parser;
    private $validator;
    private $mapping;

    private function getImportService()
    {
        if ($this->importService === null) {
            $kernel = static::bootKernel();
            $this->importService = $kernel->getContainer()->get(ImportService::class);
            $this->mapping = $kernel->getContainer()->getParameter('product.mapping');
            $this->validator = new ProductValidator($kernel->getContainer());
        }
        return $this->importService;
    }

    public function testSuccessfulImport()
    {

        $this->getImportService();
        $kernel = static::bootKernel();

        $this->parser = new CsvParser(
            "/var/www/csv-parser/tests/AppBundle/Files/stock-successful.csv",
            $this->mapping,
            Product::class);

        $this->importService->handle(new TestMode(), $this->parser, $this->validator);
        $this->assertEquals(
            $this->importService->getSuccessful(),
            $this->importService->getProcessed()
            );
    }

    public function testFailImport()
    {
        $this->getImportService();
        $kernel = static::bootKernel();

        $this->parser = new CsvParser(
            "/var/www/csv-parser/tests/AppBundle/Files/stock.csv",
            $this->mapping,
            Product::class);

        $this->importService->handle(new TestMode(), $this->parser, $this->validator);
        $this->assertEquals($this->importService->getSkipped(), 6);
    }

    public function testDublicateCodeProductImport()
    {
        $this->getImportService();
        $kernel = static::bootKernel();

        $this->parser = new CsvParser(
            "/var/www/csv-parser/tests/AppBundle/Files/stock-dublicate.csv",
            $this->mapping,
            Product::class);

        $this->importService->handle(new TestMode(), $this->parser, $this->validator);
        $this->assertEquals($this->importService->getSkipped(), 1);
    }
}