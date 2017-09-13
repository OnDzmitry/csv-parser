<?php

namespace AppBundle\Command;

use AppBundle\Entity\Product;
use AppBundle\Models\ImportMods\StandartMode;
use AppBundle\Models\ImportMods\TestMode;
use AppBundle\Models\MessageConsoleHelper;
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

        $importService = $this->getContainer()->get('app.import_service');

        if (strcasecmp($mode, 'test') === 0) {
            $importService->handle(new TestMode());
        } else {
            $importService->handle(new StandartMode($em));
        }

        $failItems = $importService->getSkippedItems();
        $output->writeln(
            MessageConsoleHelper::getProcessEndMessage(
                $importService->getProcessed(),
                $importService->getSuccessful(),
                $importService->getSkipped()
        ));
        $output->writeln(MessageConsoleHelper::getFailItemsMessage($failItems));
    }

}
