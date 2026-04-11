<?php

return [
    [
        'title' => 'Dashboard',
        'icon' => 'feather-airplay',
        'route' => 'admin.dashboard',
        'can' => ['dashboard.index'],
    ],
    [
        'title' => 'Master',
        'icon' => 'feather-database',
        'can' => ['users.index', 'roles.index'],
        'children' => [
            [
                'title' => 'Pengguna',
                'icon' => 'feather-users',
                'route' => 'admin.users.index',
                'can' => ['users.index'],
            ],
            [
                'title' => 'Peran & Izin',
                'icon' => 'feather-shield',
                'route' => 'admin.roles.index',
                'can' => ['roles.index'],
            ],
        ],
    ]
];
