<?php

session_start();
date_default_timezone_set("Asia/Taipei");

class DB {
    protected $dsn;
    protected $pdo;
    protected $table;

    function __construct($table){
        $this->dsn = "mysql:host=localhost; charset=utf8; dbname=calendar";
        $this->pdo = new PDO($this->dsn, 'root', '', []);
        $this->table = $table;
    }

    protected function a2s($array){
        $tmp = [];
        foreach($array as $key => $value){
            $tmp[] = "`$key`='$value'";
        }
        return $tmp;
    }

    function all(...$args){
        $sql = "SELECT * FROM `$this->table`";

        if(isset($args[0])){
            if(is_array($args[0])){
                $sql .= " WHERE " . join(" AND ", $this->a2s($args[0]));
            }else {
                $sql .= $args[0];
            }
        }

        if(isset($args[1])){
            $sql .= $args[1];
        }

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    function count(...$args){
        $sql = "SELECT COUNT(*) FROM `$this->table`";

        if(isset($args[0])){
            if(is_array($args[0])){
                $sql .= " WHERE " . join(" AND ", $this->a2s($args[0]));
            }else {
                $sql .= $args[0];
            }
        }

        if(isset($args[1])){
            $sql .= $args[1];
        }

        return $this->pdo->query($sql)->fetchColumn();
    }

    function find(...$args){
        $sql = "SELECT * FROM `$this->table`";

        if(isset($args[0])){
            if(is_array($args[0])){
                $sql .= " WHERE " . join(" AND ", $this->a2s($args[0]));
            }else {
                $sql .= " WHERE `id`='$args[0]'";
            }
        }

        if(isset($args[1])){
            $sql .= $args[1];
        }

        return $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
    }

    function save($arg){
        if(isset($arg['id'])){
            $id = $arg['id'];
            unset($arg['id']);
            $sql = "UPDATE `$this->table` SET " . join(", ", $this->a2s($arg)) . " WHERE `id`='$id'";
        }else {
            $sql = "INSERT INTO `$this->table`(`" . join("`, `", array_keys($arg)) . "`) VALUES('" . join("','", $arg) . "')";
        }

        return $this->pdo->exec($sql);
    }

    function del($arg){
        $sql = "DELETE FROM `$this->table`";
        if(is_array($arg)){
            $sql .= " WHERE " . join(" AND ", $this->a2s($arg));
        }else {
            $sql .= " WHERE `id`='$arg'";
        }
        return $this->pdo->exec($sql);
    }

    function q($sql){
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}

function dd($array){
    echo "<pre>";
    print_r($array);
    echo "</pre>";
}

function to($url){
    header("location: " . $url);
}

$Events = new DB("events");

?>