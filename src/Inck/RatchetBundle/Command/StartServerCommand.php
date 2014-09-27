<?php

namespace Inck\RatchetBundle\Command;

use Ratchet\App;
use Ratchet\Session\SessionProvider;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StartServerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('inck:ratchet:start')
            ->setDescription('Start the Ratchet server');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $host = $this->getContainer()->getParameter('inck_ratchet.server_host');
        $port = $this->getContainer()->getParameter('inck_ratchet.server_port');
        $name = $this->getContainer()->getParameter('inck_ratchet.server_name');

        $output->writeln(sprintf('Starting Ratchet server on port %d...', $port));

        $session = new SessionProvider(
            $this->getContainer()->get('inck_ratchet.server.server_service'),
            $this->getContainer()->get('session.handler.memcache')
        );

        $server = new App($host, $port, $host);
        $server->route('/'.$name, $session);

        $output->writeln('Server started !');
        $server->run();
    }
}