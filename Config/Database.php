<?php

class Database 
{

    private $db_host="192.168.0.110";
    private $db_name="cwm_integration";
    private $db_user="dilan";
    private $db_pass="ceylon@linux@2020";
    
    protected $connection;

    public function __construct() {
        $this->connection = new mysqli($this->db_host,$this->db_user,$this->db_pass,$this->db_name);

        if(!isset($this->connection)) {
            echo "Connection failed"; die();
        }
        else {
            return $this->connection;
        }
    }
}

?>