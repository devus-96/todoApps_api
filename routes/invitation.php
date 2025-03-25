<?php 

return [

    [
        'method' => 'GET',
        'pattern' => '#^(https?://[^/]+)?(/[invitation/create]+)(?:\?(.*))?$#',
        'controller' => 'CalendarControllers',
        'action' => 'getAll'
    ]
]


?>