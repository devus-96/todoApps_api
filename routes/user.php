<?php 

return [
        [
            'method' => 'POST',
            'pattern' => '#^/register/user(?:\?.*)?$#',
            'controller' => 'UserController',
            'action' => 'createUser'
        ],
        [
            'method' => 'POST',
            'pattern' => '#^/login/user$#',
            'controller' => 'UserController',
            'action' => 'getUser'
        ],
    ]

?>

return [
    [
        'method' => 'GET',
        'pattern' => '#^/users/(\d+)$#',
        'controller' => 'UserController',
        'action' => 'show'
    ],
    [
        'method' => 'POST',
        'pattern' => '#^/users$#',
        'controller' => 'UserController',
        'action' => 'store'
    ],
    // Ajoutez d'autres routes ici
];
