<?php 

return [
        [
            'method' => 'POST',
            'pattern' => '#^(https?://[^/]+)?(/[users/register]+)(?:\?(.*))?$#',
            'controller' => 'UserController',
            'action' => 'create'
        ],
        [
            'method' => 'POST',
            'pattern' => '#^(https?://[^/]+)?(/[users/login]+)(?:\?(.*))?$#',
            'controller' => 'UserController',
            'action' => 'get'
        ],
        [
            'method' => 'PATCH',
            'pattern' => '#^(https?://[^/]+)?(/[users/update]+)(?:\?(.*))?$#',
            'controller' => 'UserController',
            'action' => 'update'
        ],
    ]

?>
