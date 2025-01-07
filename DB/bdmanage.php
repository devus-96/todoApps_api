<?php
class BD {
   private $host = 'localhost';
   private $user = "postgres";
   private $dbname = "appmanagebd";
   private $password = "daus985220";
   public $pdo;

   
   function __construct() {
        try {
          $this->pdo = new PDO("pgsql:host=$this->host;dbname=$this->dbname",$this->user, $this->password);

          $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
 
          $this->pdo->setAttribute(PDO::ATTR_ORACLE_NULLS,PDO::NULL_EMPTY_STRING);

          $this->pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);

       } catch (PDOException $e) {
         die("Error: " . $e->getMessage());
       }
    }

    public function alterinsert ($data, $table) {
      $i = 0;
      $prevcammand = '';
      $prevcammand2 = '';
      foreach($data as $key => $value) {
         $pre = ($i > 0)?', ':''; 
         $prevcammand .= $pre.$key;
         $prevcammand2 .= $pre.':'.$key;
         $i++;
      }
      $command = 'INSERT INTO'. ' '. $table. ' ' .'('. $prevcammand. ')'.  'VALUES'. '('.$prevcammand2. ')' ;
      return $command;
    }

    public function pdotable ($data) {
        $tab = array();
        foreach ($data as $key => $value) {
          $tab[":$key"] = $value;
        }
        return $tab;
    }

}
?>