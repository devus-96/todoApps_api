<?php 

require_once $_SERVER['DOCUMENT_ROOT'] . '/database/bdmanage.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/utils/jwt.php';

class AUTH extends BD {
    public function authProvider ($data, $provider) {
        try {
            if (!empty($data)) {
                $user = new BD($data);
                $checkEmail = $user->search('users', '*', 'email');
                if ($checkEmail) {
                    $verify_provider = $user->search('users', 'provider', 'email');
                    if ($verify_provider === 'google') {
                        $user->update('users', ['github_id' => $data['github_id']]);
                    } else if ($verify_provider === "github") {
                        $user->update('users', ['google_id' => $data['google_id']]);
                    }
                    header("HTTP/1.1 200 OK");
                } else {
                    if ($provider === "github") {
                        $user->insert('users', 'github_id');
                    } else if ($provider === 'google') {
                        $user->insert('users', 'google_id');
                    }
                    header("HTTP/1.1 200 OK");
                }
            }
        } catch (PDOException $e) {
            header("HTTP/1.1 400 Bad request1");
            echo json_encode($e);
        }
    }
}

?>