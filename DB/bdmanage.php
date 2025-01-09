<?php
class BD {
   private $host = 'localhost';
   private $user = "postgres";
   private $dbname = "appmanagebd";
   private $password = "daus985220";
   private $data;
   public $pdo;

   
   function __construct($data) {
        $this->data = $data;
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

    function insert ($table) {
      $value = $this->alterinsert($this->data, $table);
      $tab = $this->pdotable($this->data);
      $user = $this->pdo->prepare($value);
      $user->execute($tab);
   }

   function select ($table, $selected, $where) {
    $get = $this->pdo->prepare("SELECT $selected FROM $table WHERE $where = :$where");
    $get->execute([":$where" => $this->data[$where]]);
    $res = $get->fetch(PDO::FETCH_ASSOC);

    return $res;
  }

}
?>