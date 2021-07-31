<?php

namespace App;

class Grid
{
    /** @var array */
    protected $data;
    
    protected $newData;
         
    public function __construct(array $data)
    {
        $data = $this->fillDeadNeighbours($data);
        $this->setState($data);
    }
    
    private function fillDeadNeighbours(array $data): array
    {
        // WIP
        //  foreach ($data as $cell)
        //      $this->getNeighbours();
        
        //         if (!isset($this->data[$x . '.' . $y])) {
        //             $this->data[$x . '.' . $y] = ['row'=>($i-1), 'col'=>($j-1), 'state'=>'dead'];
        //         } else {
        //             $alive++;
        //         }

        //         $response[$x, $y] = $this->data[$x . '.' . $y];
        //      }
        // }
    }
    
    public function process(): array
    {
        $this->newData = $this->data;
        
        $current_grid[x][y] = true;
     
        if (!isset($current_grid[x+1][y])) {
            $current_grid[x+1][y] = false;
        }
        
        // $array["2.1"]=['row'=>1, 'col'=>2, 'state'=>'alive'];
        
        // Loop through current data and add dead neighboring elements to current elements
        foreach($this->data as $key => $cell) {
            $neightbours = $this->getNeightbours($cell['row'], $cell['cell']);
            
            if ($cell['state']) {
                if ($result1 !== null) {
                    continue;
                }
            
                if () {}
            
                if () {}
            } else {
                if () {}
            }
        }
        
        return $this->newData;
    }
    
    public function setState(array $data): void
    {
        $this->newData = [];
        $this->data = $data;
    }
    
    private getNeightbours($i, $j): array
    {
        //$i = row
        // $j = col
        $response = [];
        $alive = 0;
        
        for ($x = $i -1; $x <= $i+1; $x++) {
             for ($y = $j-1; $y <= $j+1; $y++) {
                  if ($x === $i && $j === y) {
                    continue;
                }
        
                if (!isset($this->data[$x . '.' . $y])) {
                    $this->data[$x . '.' . $y] = ['row'=>($i-1), 'col'=>($j-1), 'state'=>'dead'];
                } else {
                    $alive++;
                }

                $response[$x, $y] = $this->data[$x . '.' . $y];
             }
        }
        
        return [
            'grid' => $response,
            'alive' => $alive,
        ];
    }

    // Any live cell with fewer than two live neighbours dies, as if by underpopulation.
    /**
     * @return null|bool
     */
    private function rule1(i, j) {

    }

    // Any live cell with two or three live neighbours lives on to the next generation.
    /**
     * @return null|bool
     */
    private function rule2() {

    }

    // Any live cell with more than three live neighbours dies, as if by overpopulation.
    /**
     * @return null|bool
     */
    private function rule3() {

    }
    
    // Any dead cell with exactly three live neighbours becomes a live cell, as if by reproduction.
    /**
     * @return null|bool
     */
    private function rule4() {

    }

}