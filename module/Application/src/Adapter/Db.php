<?php

namespace Application\Adapter;

use Laminas\Db\Adapter\Adapter;

class Db
{
    private $connections;
    
    public function __construct($connections)
    {
        $this->connections = $connections;
    }
    
    public function __get($connection)
    {
        return $this->getAdapter($this->connections[$connection]);
    }
    
    private function getAdapter($connection)
    {
        return new Adapter($connection);
    }
}