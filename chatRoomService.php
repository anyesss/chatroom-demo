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
        $this->pushToAllUser($this->formatSysPush("用户{$request->fd}连接了..."));

        echo "server: handshake success with fd {$request->fd}\n";
    }

    private function msgHandle(Swoole\WebSocket\Server $server, $frame)
    {
        $this->pushToAllUser($this->formatUserPush($frame->data));

        echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
    }

    private function closeHandle($ser, $fd)
    {
        $this->pushToAllUser($this->formatSysPush("用户{$fd}退出了..."));

        echo "client {$fd} closed\n";
    }

    private function pushToAllUser($msg)
    {
        foreach ($this->server->connections as $fd) {
            if ($this->server->isEstablished($fd) && $this->server->exist($fd)) {
                $this->server->push($fd, $msg);
            }
        }
    }

    private function formatSysPush($msg)
    {
        return json_encode(['type' => 0, 'msg' => '系统：' . $msg]);
    }

    private function formatUserPush($msg)
    {
        $formatMsg = json_decode($msg, true);
        $formatMsg = sprintf('%s：%s', $formatMsg['nickname'], $formatMsg['msg']);

        return json_encode(['type' => 1, 'msg' => htmlspecialchars($formatMsg)]);
    }

}

new chatRoom();
