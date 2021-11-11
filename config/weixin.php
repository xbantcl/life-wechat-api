<?php

return [
    'program' => [
        'appid'  => getenv('APPID') ? getenv('APPID') : '',
        'secret' => getenv('SECRET') ? getenv('SECRET') : '',
        'map_key' => getenv('MAP_KEY') ? getenv('MAP_KEY') : '',
        'openid'  => getenv('OPENID') ? getenv('OPENID') : '',
    ]
];