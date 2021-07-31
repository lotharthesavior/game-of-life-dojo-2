<?php

namespace App;


class MessageHandler
{
    protected $server;
    protected $frame;

    public function __construct($server, $frame)
    {
        $this->server = $server;
        $this->frame = $frame;
    }

    public function __invoke()
    {

        // var_dump($this->frame->data);
        // $this->server

        $grid = new Grid(json_decode($this->frame, true));

        $this->server->push($this->frame->fd, json_encode([
            'message' => 'new-state',
            // 'data' => $grid->process(),
        ]));
    }
}
