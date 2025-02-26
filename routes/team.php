<?php 

return [
        [
            'method' => 'POST',
            'pattern' => '#^(https?://[^/]+)?(/[team/create]+)(?:\?(.*))?$#',
            'controller' => 'TeamController',
            'action' => 'create'
        ],
        [
            'method' => 'PATCH',
            'pattern' => '#^(https?://[^/]+)?(/[team/edit]+)(?:\?(.*))?$#',
            'controller' => 'TeamController',
            'action' => 'update'
        ],
        [
            'method' => 'GET',
            'pattern' => '#^(https?://[^/]+)?(/[team/search]+)(?:\?(.*))?$#',
            'controller' => 'TeamController',
            'action' => 'search'
        ],
        [
            'method' => 'DELETE',
            'pattern' => '#^(https?://[^/]+)?(/[team/delete]+)(?:\?(.*))?$#',
            'controller' => 'TeamController',
            'action' => 'delete'
        ]
    ]

?>
