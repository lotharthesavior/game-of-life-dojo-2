<?php

namespace App;

class Grid
{
    /** @var array */
    protected $data;
    
    protected $newData;
         
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->fillDeadNeighbours();
    }
    
    private function fillDeadNeighbours()
    {
        $parsedData = $this->data;

        foreach ($this->data as $cell) {
            $parsedData = array_merge($parsedData, $this->getNeighbours($cell['row'], $cell['col'])['grid']);
        }

        $this->data = $parsedData;
    }
    
    public function process(): array
    {
        $this->newData = $this->data;
        
        // Loop through current data and add dead neighboring elements to current elements
        foreach($this->data as $key => $cell) {
            $neighbours = $this->getNeighbours($cell['row'], $cell['col']);
            
            if ($cell['state'] === 'alive') {
                // Any live cell with fewer than two live neighbours dies, as if by underpopulation.
                // Any live cell with two or three live neighbours lives on to the next generation.
                // Any live cell with more than three live neighbours dies, as if by overpopulation.
                if ($neighbours['alive'] < 2 || $neighbours['alive'] > 3) {
                    $this->newData[$key]['state'] = 'dead';
                    continue;
                }
            } else {
                // Any dead cell with exactly three live neighbours becomes a live cell, as if by reproduction.
                if ($neighbours['alive'] === 3) {
                    $this->newData[$key]['state'] = 'alive';
                    continue;
                }
            }
        }
        
        return $this->newData;
    }
    
    public function setState(array $data): void
    {
        $this->newData = [];
        $this->data = $data;
    }
    
    private function getNeighbours($i, $j): array
    {
        //$i = row
        // $j = col
        $response = [];
        $alive = 0;
        
        for ($x = $i -1; $x <= $i+1; $x++) {
            for ($y = $j-1; $y <= $j+1; $y++) {
                if ($x == $i && $j == $y) {
                    continue;
                }

                if (!isset($this->data[$x . ',' . $y])) {
                    $this->data[$x . ',' . $y] = ['row'=> $x, 'col'=> $y, 'state'=>'dead'];
                } else if ($this->data[$x . ',' . $y]['state'] === 'alive') {
                    $alive++;
                }

                $response[$x . ',' . $y] = $this->data[$x . ',' . $y];
            }
        }
        
        return [
            'grid' => $response,
            'alive' => $alive,
        ];
    }

}