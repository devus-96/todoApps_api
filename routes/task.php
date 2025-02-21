<?php 

return [
        [
            'method' => 'POST',
            'pattern' => '#^(https?://[^/]+)?(/[^?]+)(?:\?(.*))?$#',
            'controller' => 'TaskController',
            'action' => 'create'
        ],
        [
            'method' => 'PUT',
            'pattern' => '#^(https?://[^/]+)?(/[^?]+)(?:\?(.*))?$#',
            'controller' => 'TaskController',
            'action' => 'update'
        ],
        [
            'method' => 'GET',
            'pattern' => '#^(https?://[^/]+)?(/[^?]+)(?:\?(.*))?$#',
            'controller' => 'TaskController',
            'action' => 'get'
        ],
        [
            'method' => 'DELETE',
            'pattern' => '#^(https?://[^/]+)?(/[^?]+)(?:\?(.*))?$#',
            'controller' => 'TaskController',
            'action' => 'delete'
        ]
    ]

?>
