<?php

namespace Inck\NotifBundle\Command;

use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ratchet\Server\IoServer;

class ServerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('notif:server:start')
            ->setDescription('Start the notification server');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $port = $this->getContainer()->getParameter('inck_notif.server_port');
        $output->writeln(sprintf('Starting server on port %d...', $port));

        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    $this->getContainer()->get('inck_notif.server.server_service')
                )
            ),
            $port
        );

        $output->writeln('Server started !');
        $server->run();
    }
}