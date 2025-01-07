class BDManage {
   private $host = 'localhost';
   private $user = "postgres";
   private $dbname = "appmanagebd";
   private $password = "daus985220";
   private $pdo;

   
   function __construct($data = array()) {
        $this -> data = $data;
        try {
          $this -> pdo = new PDO("pgsql:host="."$this -> host". ";dbname=". "$this ->dbname". ", $user, $password");

          $this -> pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
 
          $this -> pdo->setAttribute(PDO::ATTR_ORACLE_NULLS,PDO::NULL_EMPTY_STRING);

          $this -> pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);

       } catch (PDOException $e) {
         die("Error: " . $e->getMessage());
       }
    }

    private function alterinsert ($data, $table) {
      $i = 0;
      $prevcammand = '';
      $prevcammand2 = '';
      $commandExecute = '';
      foreach($data as key => value) {
         $pre = ($i > 0)?', ':''; 
         $prevcammand .= $pre.$key
         $prevcammand2 .= $prev.':'.$key
         i++
      }
      $command = "INSERT INTO".$table .'('. $prevcammand. ')'.  'VALUES'. '('.$prevcammand2. ')' ;
      return $command
    }

    private function pdotable ($data) {
        foreach(){
          
        }
    }


    function insert ($table) {
       
    }

    function check () {

    }


}