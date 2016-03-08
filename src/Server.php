<?php
/**
 * Created by PhpStorm.
 * User: Apaar
 * Date: 2016-03-06
 * Time: 1:24 AM
 */

namespace Apaar\Chat;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

/**
 * Class Server
 * @package Apaar\Chat
 */
class Server implements MessageComponentInterface
{
    /**
     * @var ConnectionInterface[]
     */
    private $clients;

    /**
     * Server constructor.
     */
    public function __construct()
    {
        $this->clients = new \SplObjectStorage();
    }

    /**
     * @inheritDoc
     */
    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);

        $this->printMessage('New connection!', $conn);
    }

    /**
     * @inheritDoc
     */
    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);

        $this->printMessage('Connection closed!', $conn);
    }

    /**
     * @inheritDoc
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $this->printMessage('Error occurred!' . $e->getMessage(), $conn);

        $conn->close();
    }

    /**
     * @inheritDoc
     */
    public function onMessage(ConnectionInterface $from, $msg)
    {
        $this->printMessage("Message received: {$msg}", $from);

        $this->sendMessage($msg);
    }

    /**
     * Sends message to attached clients except the one sending
     *
     * @param $message
     */
    private function sendMessage($message)
    {
        foreach ($this->clients as $client) {
            $client->send($message);
        }
    }

    /**
     * Prints the given message
     *
     * @param $message
     * @param ConnectionInterface $connection
     */
    private function printMessage($message, ConnectionInterface $connection)
    {
        echo $message . ' ' . $connection->resourceId ?? '' . PHP_EOL;
    }
}
