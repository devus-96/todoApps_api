<?php 

return [
        [
            'method' => 'POST',
            'pattern' => '#^(https?://[^/]+)?(/[^?]+)(?:\?(.*))?$#',
            'controller' => 'UserController',
            'action' => 'create'
        ],
        [
            'method' => 'POST',
            'pattern' => '#^(https?://[^/]+)?(/[^?]+)(?:\?(.*))?$#',
            'controller' => 'UserController',
            'action' => 'get'
        ],
    ]

?>
