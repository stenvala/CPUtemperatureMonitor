<?php

// CPU temperatures via WebSocket
// (c) Antti Stenvall
// antti@stenvall.fi
//

require_once 'temperature.php';
require_once '../vendor/autoload.php';

class temperatureSocket extends temperature implements \Ratchet\MessageComponentInterface {

  protected $clients;

  public function __construct() {
    //parent::__construct();
    $this->clients = new \SplObjectStorage;
  }

  public function onOpen(\Ratchet\ConnectionInterface $conn) {
    // Store the new connection to send messages to later
    $this->clients->attach($conn);
    echo "New connection! ({$conn->resourceId})\n";
  }

  public function onMessage(\Ratchet\ConnectionInterface $from, $msg) {
    $numRecv = count($this->clients) - 1;
    echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
      , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');
    foreach ($this->clients as $client) {
      // The sender is not the receiver, send to each client connected
      if ($client != $from) {
        $client->send($msg);
      }
    }
  }

  public function onClose(\Ratchet\ConnectionInterface $conn) {
    // The connection is closed, remove it, as we can no longer send it messages
    $this->clients->detach($conn);
    echo "Connection {$conn->resourceId} has disconnected\n";
  }

  public function onError(\Ratchet\ConnectionInterface $conn, \Exception $e) {
    echo "An error has occurred: {$e->getMessage()}\n";
    $conn->close();
  }

  public function broadcast() {
    $this->setTemperatures();
    $t = json_encode($this->getTemperatures());
    foreach ($this->clients as $client) {
      $client->send($t);
    }
  }
}

?>
