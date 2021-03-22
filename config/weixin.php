<?php

return [
    'program' => [
        'appid'  => getenv("APPID") ? getenv("APPID") : '',
        'secret' => getenv("SECRET") ? getenv("SECRET") : ''
    ]
];