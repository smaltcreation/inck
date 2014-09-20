<?php

namespace Inck\NotifBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConfigureCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('inck:notif:configure')
            ->setDescription('Configure the notif bundle');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $query = "CREATE TABLE IF NOT EXISTS `session` (
            `session_id` varchar(255) NOT NULL,
            `session_value` text NOT NULL,
            `session_time` int(11) NOT NULL,
            PRIMARY KEY (`session_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $this
            ->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getConnection()
            ->prepare($query)
            ->execute();

        $output->writeln('NotifBundle configured');
    }
}