<?php
require __DIR__ . '/vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class InventoryWS implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage();
        echo "WebSocket server started...\n";
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "✅ New client connected: {$conn->remoteAddress} (Total: {$this->clients->count()})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        echo "📨 Received: $msg — broadcasting to {$this->clients->count()} clients\n";

        // Broadcast to ALL clients INCLUDING sender
        foreach ($this->clients as $client) {
            $client->send($msg);
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        echo "🔴 Client disconnected: {$conn->remoteAddress} (Total: {$this->clients->count()})\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "❌ Error: {$e->getMessage()}\n";
        $conn->close();
    }
}

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

$port = 8081;
$host = '0.0.0.0';

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new InventoryWS()
        )
    ),
    $port,
    $host
);

echo "🚀 Server running at ws://$host:$port\n";
$server->run();