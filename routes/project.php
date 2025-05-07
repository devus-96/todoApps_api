<?php 

return [
        [
            'method' => 'POST',
            'pattern' => '#^(https?://[^/]+)?(/[project/create]+)(?:\?(.*))?$#',
            'controller' => 'ProjectController',
            'action' => 'create'
        ],
        [
            'method' => 'PATCH',
            'pattern' => '#^(https?://[^/]+)?(/[project/edit]+)(?:\?(.*))?$#',
            'controller' => 'ProjectController',
            'action' => 'update'
        ],
        [
            'method' => 'GET',
            'pattern' => '#^(https?://[^/]+)?(/[project/search]+)(?:\?(.*))?$#',
            'controller' => 'ProjectController',
            'action' => 'search'
        ],
        [
            'method' => 'GET',
            'pattern' => '#^(https?://[^/]+)?(/[project/get]+)(?:\?(.*))?$#',
            'controller' => 'ProjectController',
            'action' => 'get'
        ],
        [
            'method' => 'DELETE',
            'pattern' => '#^(https?://[^/]+)?(/[project/delete]+)(?:\?(.*))?$#',
            'controller' => 'ProjectController',
            'action' => 'delete'
        ]
    ]

?>
