<?php
class BD {
   private $host = 'localhost';
   private $user = "postgres";
   private $dbname = "appmanagebd";
   private $password = "daus985220";
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

    public function alterinsert (array $data, string $table): string {
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
    
    public function pdotable ($data): array {
        $tab = array();
        foreach ($data as $key => $value) {
          $tab[":$key"] = $value;
        }
        return $tab;
    }

    function insert (string $table): bool {
      $value = $this->alterinsert($this->data, $table);
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

  public function update (string $table, mixed $id): bool {
    foreach ($this->data as $key => $value) {
        $alter = $this->pdo->prepare("ALTER TABLE $table WHERE id = :id ALTER COLUNM $key = :value");
        $response = $alter->execute([":id" => $id, ":value" => $value]);

        return $response;
    }
  }

  public function delete (string $table, mixed $id): bool {
    $drop = $this->pdo->prepare("DROP TABLE $table WHERE id = :id");
    $response = $drop->execute([":id" => $id['id']]);

    return $response;
  }

}
?>