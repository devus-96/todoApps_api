<?php

class BD {
   private $host = HOST;
   private $user = BD_USER;
   private $dbname = DB_NAME;
   private $password = DB_PASS;
   public $data;
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

    private function alterdata (array $data, string $table): string {
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

    private function alterDataPatch (array $data, string $table, mixed $where) {
      $i = 0;
      $j = 0;
      $prevcammand = '';
      $command = '';
      $where_condition = '';
      foreach($data as $key => $value) {
        $pre = ($i > 0)?', ':''; 
        $prevcammand .= $pre.$key = ':'.$key;
        $i++;
      }
      foreach ($where as $key => $value) {
        $pre = ($j > 0 && $j < count($where) - 1)?' AND ':''; 
        $where_condition = $pre.$key = $value;
        $i++;
      }
      $command = "UPDATE TABLE $table SET $prevcammand  WHERE $where_condition";
      return $command;
    }
    
    public function pdotable ($data): array {
        $tab = array();
        foreach ($data as $key => $value) {
          $tab[":$key"] = $value;
        }
        return $tab;
    }

    function insert (string $table): bool {
      $value = $this->alterdata($this->data, $table);
      $tab = $this->pdotable($this->data);
      $user = $this->pdo->prepare($value);
      $response = $user->execute($tab);

      return $response;
   }

   function search (string $table, string $selected, string $where): mixed {
    $get = $this->pdo->prepare("SELECT $selected FROM $table WHERE $where = :$where");
    $get->execute([":$where" => $this->data[$where]]);
    $res = $get->fetch(PDO::FETCH_ASSOC);

    return $res;
  }

  public function update (string $table, mixed $params): bool {
        $value = $this->alterDataPatch($this->data, $table, $params);
        $tab = $this->pdotable($this->data);
        $alter = $this->pdo->prepare($value);
        $response = $alter->execute($tab);

        return $response;
  }

  public function delete (string $table, mixed $id): bool {
    $drop = $this->pdo->prepare("DROP TABLE $table WHERE id = :id");
    $response = $drop->execute([":id" => $id['id']]);

    return $response;
  }

}
?>