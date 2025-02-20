<?php 

return [
        [
            'method' => 'POST',
            'pattern' => '#^/create/taks(?:\?.*)?$#',
            'controller' => 'TaskController',
            'action' => 'create'
        ],
        [
            'method' => 'PUT',
            'pattern' => '#^/update/user(?:\?.*)?$#',
            'controller' => 'TaskController',
            'action' => 'update'
        ],
        [
            'method' => 'GET',
            'pattern' => '#^/search/user(?:\?.*)?$#',
            'controller' => 'TaskController',
            'action' => 'get'
        ],
        [
            'method' => 'DELETE',
            'pattern' => '#^/delete/user(?:\?.*)?$#',
            'controller' => 'TaskController',
            'action' => 'delete'
        ]
    ]

?>
