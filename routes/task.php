<?php 

return [
        [
            'method' => 'POST',
            'pattern' => '#^(https?://[^/]+)?(/[task/create]+)(?:\?(.*))?$#',
            'controller' => 'TaskController',
            'action' => 'create'
        ],
        [
            'method' => 'PATCH',
            'pattern' => '#^(https?://[^/]+)?(/[task/edit]+)(?:\?(.*))?$#',
            'controller' => 'TaskController',
            'action' => 'update'
        ],
        [
            'method' => 'GET',
            'pattern' => '#^(https?://[^/]+)?(/[task/get]+)(?:\?(.*))?$#',
            'controller' => 'TaskController',
            'action' => 'get'
        ],
        [
            'method' => 'DELETE',
            'pattern' => '#^(https?://[^/]+)?(/[task/delete]+)(?:\?(.*))?$#',
            'controller' => 'TaskController',
            'action' => 'delete'
        ]
    ]

?>
