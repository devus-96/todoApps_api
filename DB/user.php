<?php 

require 'bdmanage.php';

class Users extends BD {

    private $data;

    function __construct($data = array())
    {
        parent::__construct();
        $this->data = $data;
    }
    //la fonction new PDO()->prepare() prend en parametre une requette sql valide en string qui sera...
    //...executer par la fonction execute() de la valeur renvoye par prepare()
    function insert ($table) {
        $value = $this->alterinsert($this->data, $table);
        $tab = $this->pdotable($this->data);
        $user = $this->pdo->prepare($value);
        $user->execute($tab);
     }
}

?>