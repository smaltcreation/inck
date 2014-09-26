<?php

namespace Inck\RatchetBundle\Command;

use Ratchet\Http\HttpServer;
use Ratchet\Session\SessionProvider;
use Ratchet\WebSocket\WsServer;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ratchet\Server\IoServer;

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
        $port = $this->getContainer()->getParameter('inck_ratchet.server_port');
        $output->writeln(sprintf('Starting Ratchet server on port %d...', $port));

        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new SessionProvider(
                        $this->getContainer()->get('inck_ratchet.server.server_service'),
                        $this->getContainer()->get('session.handler.memcache')
                    )
                )
            ),
            $port
        );

        $output->writeln('Server started !');
        $server->run();
    }
}