<?php

namespace AppBundle\Command;

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
            ->addArgument('path', InputArgument::REQUIRED, 'Path to file')
            ->addOption('mode', InputOption::VALUE_OPTIONAL, 'Have one mode: \'test\'')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $argument = $input->getArgument('argument');

        if ($input->getOption('option')) {
            // ...
        }

        $output->writeln('Command result.');
    }

}
