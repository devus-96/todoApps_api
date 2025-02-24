<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/config.php';

class BD {
   private $host = HOST;
   private $user = BD_USER;
   private $dbname = DB_NAME;
   private $password = DB_PASS;
   protected $data;
   protected $pdo;

   
   public function __construct($data) {
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

    private function alter_insert_req (array $data, string $table, $result): string {
      $i = 0;
      $prevcammand = '';
      $prevcammand2 = '';
      foreach($data as $key => $value) {
         $pre = ($i > 0)?', ':''; 
         $prevcammand .= $pre.$key;
         $prevcammand2 .= $pre.':'.$key;
         $i++;
      }
      return 'INSERT INTO'. ' '. $table. ' ' .'('. $prevcammand. ')'.  'VALUES'. '('.$prevcammand2. ')' . 'RETURNING ' . $result ;
    }

    private function alter_search_req (array $data, string $table, mixed $elt): string {
      $i = 0;
      $prevcammand = '';
      foreach($data as $key => $value) {
        $pre = ($i > 0)?' AND ':''; 
        $prevcammand .= "$pre$key = :$key";
        $i++;
      }
      return "SELECT $elt FROM $table WHERE $prevcammand";
    }

    private function alter_delete_req (string $table, mixed $where) {
      $j = 0;
      $where_condition = ''; 
      foreach ($where as $key => $value) {
        $pre = ($j > 0 && $j < count($where))?' AND ':''; 
        $where_condition .= "$pre$key = $value";
        $j++;
      }
      $command = "DELETE FROM $table WHERE $where_condition";
      return $command;
    }

    private function alter_update_req (array $data, string $table, mixed $where) {
      $i = 0;
      $j = 0;
      $prevcammand = '';
      $command = '';
      $where_condition = ''; 
      foreach($data as $key => $value) {
        $pre = ($i > 0)?', ':''; 
        $prevcammand .= "$pre$key = :$key";
        $i++;
      }
      foreach ($where as $key => $value) {
        $pre = ($j > 0 && $j < count($where))?' AND ':''; 
        $where_condition .= "$pre$key = $value";
        $j++;
      }
      $command = "UPDATE $table SET $prevcammand  WHERE $where_condition";
      return  $command;
    }
    
    public function pdotable ($data): array {
        $tab = array();
        foreach ($data as $key => $value) {
          $tab[":$key"] = $value;
        }
        return $tab;
    }

    function insert (string $table, $result): mixed {
      $value = $this->alter_insert_req($this->data, $table, $result);
      $tab = $this->pdotable($this->data);
      $user = $this->pdo->prepare($value);
      $user->execute($tab);
      $result = $user->fetch(PDO::FETCH_ASSOC);

      return $result;
   }

   function get (string $table, string $elt) {
    $value = $this->alter_search_req($this->data, $table, $elt);
    $tab = $this->pdotable($this->data);
    $get = $this->pdo->prepare($value);
    $get->execute($tab);
    $res = $get->fetchAll(PDO::FETCH_CLASS);

    return $res;
   }

   function search (string $table, string $selected, string $where): mixed {
    $get = $this->pdo->prepare("SELECT $selected FROM $table WHERE $where = :$where");
    $get->execute([":$where" => $this->data[$where]]);
    $res = $get->fetch(PDO::FETCH_ASSOC);

    return $res;
  }

  public function update (string $table, mixed $params): bool {
        $value = $this->alter_update_req($this->data, $table, $params);
        $tab = $this->pdotable($this->data);
        $alter = $this->pdo->prepare($value);
        $response = $alter->execute($tab);

        return $response;
  }

  public function delete (string $table, mixed $params): bool {
    $value = $this->alter_delete_req($table, $params);
    $drop = $this->pdo->prepare($value);
    $response = $drop->execute();

    return $response;
  }

}
?>