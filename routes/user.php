<?php 

return [
        [
            'method' => 'POST',
            'pattern' => '#^(https?://[^/]+)?(/[user/register]+)(?:\?(.*))?$#',
            'controller' => 'UserController',
            'action' => 'create'
        ],
        [
            'method' => 'POST',
            'pattern' => '#^(https?://[^/]+)?(/[user/login]+)(?:\?(.*))?$#',
            'controller' => 'UserController',
            'action' => 'get'
        ],
        [
            'method' => 'PATCH',
            'pattern' => '#^(https?://[^/]+)?(/[user/update]+)(?:\?(.*))?$#',
            'controller' => 'UserController',
            'action' => 'update'
        ],
        [
            'method' => 'DELETE',
            'pattern' => '#^(https?://[^/]+)?(/[user/delete]+)(?:\?(.*))?$#',
            'controller' => 'UserController',
            'action' => 'delete'
        ],
    ]

?>
