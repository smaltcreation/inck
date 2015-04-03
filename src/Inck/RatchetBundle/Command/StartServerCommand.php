<?php

namespace Inck\RatchetBundle\Command;

use Ratchet\App;
use Ratchet\Session\SessionProvider;
use Ratchet\Wamp\WampServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Factory;
use React\ZMQ\Context;
use SessionHandlerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ZMQ;

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
	    // Parameters
        $host       = $this->getContainer()->getParameter('inck_ratchet.server_host');
        $port       = $this->getContainer()->getParameter('inck_ratchet.server_port');
        $address    = $this->getContainer()->getParameter('inck_ratchet.server_address');
        $path       = $this->getContainer()->getParameter('inck_ratchet.server_path');
	    $origin     = $this->getContainer()->getParameter('inck_ratchet.allowed_origin');
	    $zmqPort    = $this->getContainer()->getParameter('inck_ratchet.zmq_port');

        $output->writeln(sprintf('Starting Ratchet server on port %d...', $port));

	    // App
	    $loop = Factory::create();
	    $app = new App($host, $port, $address, $loop);

		// Controller
	    $server = $this->getContainer()->get('ratchet.server');

	    /** @var SessionHandlerInterface $sessionHandler */
	    $sessionHandler = $this->getContainer()->get(
		    $this->getContainer()->getParameter('session_handler')
	    );

	    $controller = new SessionProvider($server, $sessionHandler);
        $app->route('/'.$path, $controller, array($origin));

	    // Server messages
	    $context = new Context($loop);

	    $pull = $context->getSocket(ZMQ::SOCKET_PULL);
	    $pull->bind(sprintf('tcp://%s:%s', $address, $zmqPort));
	    $pull->on('message', array($server, 'onServerMessage'));

	    // Run
        $output->writeln('Server started !');
        $app->run();
    }
}
