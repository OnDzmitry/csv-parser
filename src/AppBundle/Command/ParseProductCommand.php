<?php

namespace AppBundle\Command;

use AppBundle\ImportMods\StandartMode;
use AppBundle\ImportMods\TestMode;
use League\Csv\Reader;
use League\Csv\Statement;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Services\ImportService;

class ParseProductCommand extends ContainerAwareCommand
{
    private $em;

    protected function configure()
    {
        $this
            ->setName('app:parse-product')
            ->setDescription('Parse command')
            ->addArgument('filePath', InputArgument::REQUIRED, 'Path to file')
            ->addOption('mode', null, InputOption::VALUE_NONE, 'Have one mode: \'test\'')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $filePath = $input->getArgument('filePath');
        $mode = $input->getOption('mode');

        $p = $this->getContainer()->getParameter('mapping');

        $importService = $this->getContainer()->get(ImportService::class);

        if (strcasecmp($mode, 'test') === 0) {
            $importService->handle($filePath, new TestMode());
        } else {
            $importService->handle($filePath, new StandartMode($this->em));
            $output->writeln($importService->getResultMessage());
            $output->writeln($importService->getFailProductsMessage());
        }
    }

}
