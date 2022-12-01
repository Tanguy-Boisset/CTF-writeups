<?php

class DB {
    function __construct(){
        try {
            $this->con = new PDO("sqlite://".__DIR__."/database.sql");
        } catch (PDOException $e){
            die($e->getMessage());
        }
    }
}

$db = new DB();