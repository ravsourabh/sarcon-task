<?php
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use MyApp\Chat;

    require('vendor/autoload.php');

    $server = IoServer::factory(
        new HttpServer(new WsServer(new Chat())),
        9999
    );

    $server->run();
?>