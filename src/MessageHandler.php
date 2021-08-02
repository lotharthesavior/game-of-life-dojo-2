<?php

namespace App;

class MessageHandler
{
    protected $server;
    protected $frame;
    protected $grid;

    public function __construct($server, $frame, $grid)
    {
        $this->server = $server;
        $this->frame = $frame;
        $this->grid = $grid;
    }

    public function __invoke()
    {
        $parsedData = json_decode($this->frame->data, true);


        if ($parsedData['action'] === 'start-game') {
            $this->startGame($parsedData);
            return;
        } else if ($parsedData['action'] === 'new-state') {
            $this->newState($parsedData);
            return;
        }

        echo "Not expected action. Data: " . json_encode($parsedData);
    }

    private function startGame(array $parsedData)
    {
        $this->grid->set(GLOBAL_GRID_SIZE_KEY, [
            'gridsize' => json_encode($parsedData['data']['gridSize'])
        ]);
    }

    private function newState(array $parsedData)
    {
        $start_memory = memory_get_usage();
        $start = microtime(true);
        
        // $data = (new Grid($parsedData['data']['grid'], $this->grid))->process();
        $data = (new GridCoroutine($parsedData['data']['grid'], $this->grid))->process();

        $used_memory = memory_get_usage() - $start_memory;
        $time_elapsed_secs = microtime(true) - $start;
        echo "\nExecution time: " . $time_elapsed_secs . "s\n";
        echo "User memory: " . $used_memory . "\n";

        $this->server->push($this->frame->fd, json_encode([
            'message' => 'new-state',
            'data' => $data,
        ]));
    }
}
