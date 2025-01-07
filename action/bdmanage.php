class BDManage {
   private $host = 'localhost';
   private $user = "postgres";
   private $dbname = "appmanagebd";
   private $password = "daus985220";
   private $pdo;

   
   function __construct($data = array()) {
        try {
          $this -> pdo = new PDO("pgsql:host="."$this -> host". ";dbname=". "$this ->dbname". ", $user, $password");

          $this -> pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
 
          $this -> pdo->setAttribute(PDO::ATTR_ORACLE_NULLS,PDO::NULL_EMPTY_STRING);

          $this -> pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);

       } catch (PDOException $e) {
         die("Error: " . $e->getMessage());
       }
    }

    function insert () {
                //la fonction new       PDO()->prepare() prend en parametre une requette sql valide en string qui sera...
        $stmg = $pdo->prepare("INSERT INTO users (firstname, lastname, email, password, role) VALUES (:firstName, :lastName, :email, :password, :role)");
        //...executer par la fonction execute() de la valeur renvoye par prepare()
        $stmg->execute([
            ":firstName" => $data["firstName"],
            ":lastName" => $data["lastName"],
            ":email" => $data["email"],
            ":password" => $encrpt,
            "role" => 'administrator',
        ]);
    }

    function check () {

    }


}