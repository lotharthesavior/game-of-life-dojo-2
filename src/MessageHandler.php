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
        $parsedData = json_decode($this->frame->data, true);

        // var_dump($this->frame);
        // var_dump(json_decode($this->frame->data, true));
        // $this->server

        $grid = new Grid($parsedData['data']['grid']);

        $this->server->push($this->frame->fd, json_encode([
            'message' => 'new-state',
            'data' => $grid->process(),
        ]));
    }
}
