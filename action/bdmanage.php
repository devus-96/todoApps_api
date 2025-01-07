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

    private function alter ($data) {
      $i = 0;
      $cammand = '';
      $commandExecute = '';
      foreach($data as key => value) {
         $pre = ($i > 0)?', ':''; 
         $command .= $pre.$key
         
      }
      return $command
    }


    function insert ($table) {
       
    }

    function check () {

    }


}