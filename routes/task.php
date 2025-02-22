<?php 

return [
        [
            'method' => 'POST',
            'pattern' => '#^(https?://[^/]+)?(/[^?]+)(?:\?(.*))?$#',
            'pathname' => '',
            'controller' => 'TaskController',
            'action' => 'create'
        ],
        [
            'method' => 'PUT',
            'pattern' => '#^(https?://[^/]+)?(/[^?]+)(?:\?(.*))?$#',
            'pathname' => '',
            'controller' => 'TaskController',
            'action' => 'update'
        ],
        [
            'method' => 'GET',
            'pattern' => '#^(https?://[^/]+)?(/[^?]+)(?:\?(.*))?$#',
            'pathname' => '',
            'controller' => 'TaskController',
            'action' => 'get'
        ],
        [
            'method' => 'DELETE',
            'pattern' => '#^(https?://[^/]+)?(/[^?]+)(?:\?(.*))?$#',
            'pathname' => '',
            'controller' => 'TaskController',
            'action' => 'delete'
        ]
    ]

?>
