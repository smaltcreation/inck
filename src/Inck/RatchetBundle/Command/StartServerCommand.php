<?php

namespace Inck\RatchetBundle\Command;

use Ratchet\App;
use Ratchet\Session\SessionProvider;
//use React\EventLoop\Factory;
use SessionHandlerInterface;
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

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $host       = $this->getContainer()->getParameter('inck_ratchet.server_host');
        $port       = $this->getContainer()->getParameter('inck_ratchet.server_port');
        $address    = $this->getContainer()->getParameter('inck_ratchet.server_address');
        $path       = $this->getContainer()->getParameter('inck_ratchet.server_path');
	    $origin     = $this->getContainer()->getParameter('inck_ratchet.allowed_origin');

        $output->writeln(sprintf('Starting Ratchet server on port %d...', $port));

        /** @var SessionHandlerInterface $sessionHandler */
        $sessionHandler = $this->getContainer()->get(
            $this->getContainer()->getParameter('session_handler')
        );

        $sessionProvider = new SessionProvider(
            $this->getContainer()->get('inck_ratchet.server.server'),
            $sessionHandler
        );

        $server = new App($host, $port, $address);
        $server->route('/'.$path, $sessionProvider, array($origin));

        $output->writeln('Server started !');
        $server->run();
    }
}
