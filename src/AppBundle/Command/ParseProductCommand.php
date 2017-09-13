<?php

namespace AppBundle\Command;

use AppBundle\Classes\ImportMods\StandartMode;
use AppBundle\Classes\ImportMods\TestMode;
use AppBundle\Classes\MessageConsoleHelper;
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
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $importService = $this->getContainer()->get('app.import_service');

        if (strcasecmp($mode, 'test') === 0) {
            $importService->handle($filePath, new TestMode());
        } else {
            $importService->handle($filePath, new StandartMode($em));
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
