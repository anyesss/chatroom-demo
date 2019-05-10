<?php

class chatRoom
{
    public $server;

    public function __construct()
    {
        $this->server = new Swoole\WebSocket\Server('0.0.0.0', 9501);

        $this->server->on('open', function (swoole_websocket_server $server, $request) {
            $this->openHandle($server, $request);
        });

        $this->server->on('message', function (Swoole\WebSocket\Server $server, $frame) {
            $this->msgHandle($server, $frame);
        });

        $this->server->on('close', function ($ser, $fd) {
            $this->closeHandle($ser, $fd);
        });

        $this->server->start();
    }

    private function openHandle(swoole_websocket_server $server, $request)
    {
        echo "server: handshake success with fd {$request->fd}\n";
    }

    private function msgHandle(Swoole\WebSocket\Server $server, $frame)
    {
        echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
        $server->push($frame->fd, "this is server");
    }

    private function closeHandle($ser, $fd)
    {
        echo "client {$fd} closed\n";
    }

}

new chatRoom();
