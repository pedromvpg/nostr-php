<?php

namespace swentel\nostr\Relay;

use swentel\nostr\MessageInterface;
use swentel\nostr\RelayInterface;
use swentel\nostr\CommandResultInterface;
use WebSocket;

class Relay implements RelayInterface
{

    /**
     * The relay URL.
     *
     * @var string
     */
    private string $url;

    /**
     * the payload to send.
     *
     * @var string
     */
    private string $payload;

    /**
     * Constructs the Relay.
     *
     * @param string $websocket
     *   The socket URL.
     */
    function __construct(string $websocket, MessageInterface $message)
    {
        // TODO validate URL.
        $this->url = $websocket;
        $this->payload = $message->generate();
    }

    /**
     * {@inheritdoc}
     */
    public function send(): CommandResultInterface
    {
      try {
        $client = new WebSocket\Client($this->url);
        $client->text($this->payload);
        $response = $client->receive();
        $client->close();
        $response = json_decode($response);
      } catch (WebSocket\ConnectionException $e) {
        $response = [
          'OK',
          '',
          false,
          $e->getMessage()
        ];
      }
      return new CommandResult($response);
    }
}
