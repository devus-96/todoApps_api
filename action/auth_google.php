<?php 
declare(strict_types = 1);

require '../action/jwt.php';

class User {
    private $dbHost = DB_HOST;
    private $dbUserName = DB_USERNAME;
    private $dbPassword = DB_PASSWORD;
    private $dbName  = DB_NAME;
    private $userTbl = DB_USER_TBL;
    private $db;

    function __construct ()
    {
        if (isset($this -> db)) {
            try {
                $pdo = new PDO("pgsql:host= $this -> dbHost;dbname=$this -> dbName", $this -> dbUserName, $this -> dbPassword);
                $this -> db = $pdo;
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Error: " . $e->getMessage());
            }
        }
    }

    function checkUser ($data = array()) {
        require 'db.php';
        if (!empty($data)) {
            $checkQuery = "SELECT * FROM" . $this->userTbl . "WHERE oauth_provider = '" .$data [ 'oauth_provider' ]. "'AND oauth_uid = '" . $data['oauth_uid']. "'";
            $smth = $this -> db -> prepare($checkQuery);
            $checkResult = $smth->fetch(PDO::FETCH_ASSOC);
        }

        if (!array_key_exists('modified', $data)) {
            $data["modified"] = date('Y-m-d H:i:s');
        }

        if (count($checkResult) > 0) {
            $colvalset = '';
            $i = 0;
            foreach ($data as $key => $val) {
                $pre = ($i > 0) ?', ' : '';
                $colvalset .= $pre.$key . "='" . "$val";
                $i++;
            }
            $whereSql = "WHERE oauth_provider = '" .$data [ 'oauth_provider' ]. "'AND oauth_uid = '" . $data['oauth_uid']. "'";
            $updare = 'UPDATE' . $this -> userTbl . "SET" . $colvalset . $whereSql;
            $smth2 = $this -> db -> prepare($updare);
            $smth2 -> execute();
        } else {
            if (!array_key_exists('created', $data)) {
                $data["created"] = date('Y-m-d H:i:s');
                $columns = $values =  '';
                $i = 0;
                foreach ($data as $key => $val) {
                    $pre = ($i > 0) ?', ' : '';
                    $columns .= $pre.$key;
                    $values .= $pre."'".$val;
                    $i++;
                }
                $insert = "INSERT INTO" . $this -> userTbl . " (" .$columns. ") VALUES (".$values. ")";
                $smth3 = $this -> db -> prepare($insert);
                $smth3 -> execute();
            }
        }
        return !empty($checkResult) ? $checkResult : false;
    }
}

?>