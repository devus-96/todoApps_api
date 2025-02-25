<?php 

return [
        [
            'method' => 'PATCH',
            'pattern' => '#^(https?://[^/]+)?(/[calendar/update]+)(?:\?(.*))?$#',
            'controller' => 'CalendarControllers',
            'action' => 'update'
        ],
        [
            'method' => 'GET',
            'pattern' => '#^(https?://[^/]+)?(/[calendar/get]+)(?:\?(.*))?$#',
            'controller' => 'CalendarControllers',
            'action' => 'get'
        ],
        [
            'method' => 'GET',
            'pattern' => '#^(https?://[^/]+)?(/[calendar/search]+)(?:\?(.*))?$#',
            'controller' => 'CalendarControllers',
            'action' => 'getAll'
        ]
    ]

?>