<?php

class chatRoom
{
    public $server;

    public function __construct()
    {
        $this->server = new Swoole\WebSocket\Server('0.0.0.0', 9501);

        $this->server->on('open', 'openHandle');
        $this->server->on('message', 'msgHandle');
        $this->server->on('close', 'closeHandle');

        $this->server->start();
    }

    public function openHandle(swoole_websocket_server $server, $request)
    {
        echo "server: handshake success with fd {$request->fd}\n";
    }

    public function msgHandle(Swoole\WebSocket\Server $server, $frame)
    {
        echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
        $server->push($frame->fd, "this is server");
    }

    public function closeHandle($ser, $fd)
    {
        echo "client {$fd} closed\n";
    }

}

new chatRoom();
