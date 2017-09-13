<?php

namespace AppBundle\Command;

use AppBundle\Entity\Product;
use AppBundle\Models\ImportMods\StandartMode;
use AppBundle\Models\ImportMods\TestMode;
use AppBundle\Models\MessageConsoleHelper;
use AppBundle\Models\Parsers\CsvParser;
use AppBundle\Models\Validators\ProductValidator;
use AppBundle\Services\ImportService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ParseProductCommand extends ContainerAwareCommand
{
        protected function configure()
    {
        $this
            ->setName('app:parse-product')
            ->setDescription('Parse command')
            ->addArgument('file_path', InputArgument::REQUIRED, 'Path to file')
            ->addOption('mode', null, InputOption::VALUE_NONE, 'Have one mode: \'test\'')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filePath = $input->getArgument('file_path');
        $mode = $input->getOption('mode');
        $mapping = $this->getContainer()->getParameter('product.mapping');
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $validator = $this->getContainer()->get('validator');

        $importService = $this->getContainer()->get('app.import_service');
        $productRepository = $em->getRepository(Product::class);

        $parser = new CsvParser($filePath, $mapping, Product::class);
        $validator = new ProductValidator($productRepository, $validator);

        if (strcasecmp($mode, 'test') === 0) {
            $importService->handle(new TestMode(), $parser, $validator);
        } else {
            $importService->handle(new StandartMode($em), $parser, $validator);
        }

        $failItems = $validator->getSkippedItems();
        $output->writeln(
            MessageConsoleHelper::getProcessEndMessage(
                $parser->getProcessedCount(),
                $importService->getSuccessful(),
                $importService->getSkipped()
        ));
        $output->writeln(MessageConsoleHelper::getFailItemsMessage($failItems));
    }

}
