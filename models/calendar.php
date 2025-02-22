<?php
require $_SERVER['DOCUMENT_ROOT'] . '/bdmanage.php';

class Calendar extends BD {
   public function selectByMonthAndWeek  ($params) {
        $get = $this->pdo->prepare("SELECT * FROM calendar WHERE startdate BETWEEN :startdate AND :enddate AND user_id = :userId");
        $get->execute([
            ":startdate" => $params['startdate'],
            ":enddate" => $params['enddate'],
            ":userId" => $params['id']
        ]);
        $response = $get->fetch(PDO::FETCH_ASSOC);

        return $response;
   }

   public function selectByDay ($params) {
    $get = $this->pdo->prepare("SELECT * FROM calendar WHERE startdate = :startdate AND user_id = :userId");
    $get->execute([
        ":startdate" => $params['startdate'],
        ":userId" => $params['id'],
    ]);
    $response = $get->fetch(PDO::FETCH_ASSOC);

    return $response;
   }
}

?>