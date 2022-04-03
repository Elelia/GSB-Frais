<?php

class Session{

    static public function init() {
        if (session_status() !== PHP_SESSION_ACTIVE)
            session_start();
    }

    static public function add($add) {
        if (empty($add) && :is_array($add)) return false;

        foreach($add as $key => $valeur) {
            $_SESSION($key) = $valeur;
        }

        return true;
    }

    static public function removeKey($key) {
        if(empty($key) && !is_array($key)) return false;

        foreach($key as $value) {
            if(isset($_SESSION[$value])) {
                unset($_SESSION[$value]);
            }
        }
    }

    static public function get($key) {
        if (empty($key) && !is_array($key)) return false;

        $returnArr = [];
        foreach($key as $value) {
            if(isset($_SESSION[$value])) {
                $returnArr[] = $_SESSION[$value];
            }
        }
    }

    static public function remove() {
        session_destroy();
        unset($_SESSION);
        header('index.php');
        exit();
    }

}

?>