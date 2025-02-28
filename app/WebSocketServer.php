<?php

require 'vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class WebSocketServer implements MessageComponentInterface
{
    protected $clients = [];

    public function __construct() {
        $this->clients = [];
    }

    public function onOpen(ConnectionInterface $conn) {
        $queryParams = $conn->httpRequest->getUri()->getQuery();
        parse_str($queryParams, $params);

        if (!isset($params['game_code'])) {
            $conn->close();
            return;
        }

        $gameCode = $params['game_code'];
        $this->clients[$gameCode][$conn->resourceId] = $conn;

        echo "New connection to game: $gameCode ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);
        if (!isset($data['game_code'])) return;

        $gameCode = $data['game_code'];

        if ($data['type'] === "update_players") {
            $this->broadcastToAll($gameCode, ["type" => "update_players", "players" => $data['players']]);
        } elseif ($data['type'] === "lobby_closed") {
            $this->broadcastToAll($gameCode, ["type" => "lobby_closed"]);
        } elseif ($data['type'] === "game_started") {
            $this->broadcastToAll($gameCode, ["type" => "game_started"]);
        } elseif ($data['type'] === "player_left") {
            $this->broadcastToAll($gameCode, ["type" => "update_players"]);
        }
    }


    public function onClose(ConnectionInterface $conn) {
        foreach ($this->clients as $gameCode => &$connections) {
            if (isset($connections[$conn->resourceId])) {
                unset($connections[$conn->resourceId]);
                echo "Connection {$conn->resourceId} closed in game: $gameCode\n";

                // Send a request to remove the player from the database
                $this->notifyServerPlayerLeft($gameCode);

                // Notify remaining players
                $this->broadcastToAll($gameCode, ["type" => "update_players"]);
                break;
            }
        }
    }

    // Send a request to the CodeIgniter server to remove the player
    private function notifyServerPlayerLeft($gameCode) {
        $url = "http://localhost/leave_lobby/" . $gameCode;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_exec($ch);
        curl_close($ch);

        echo "WebSocket forced player leave for: " . $gameCode . "\n";
    }



    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        $conn->close();
    }

    private function broadcastToAll($gameCode, $message) {
        if (isset($this->clients[$gameCode])) {
            foreach ($this->clients[$gameCode] as $client) {
                $client->send(json_encode($message));
            }
        }
    }

}
