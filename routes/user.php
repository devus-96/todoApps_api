<?php 

return [
        [
            'method' => 'POST',
            'pattern' => '#^/register/user(?:\?.*)?$#',
            'controller' => 'UserController',
            'action' => 'create'
        ],
        [
            'method' => 'POST',
            'pattern' => '#^/login/user(?:\?.*)?$#',
            'controller' => 'UserController',
            'action' => 'get'
        ],
    ]

?>
