<?php

return [
    'program' => [
        'appid'      => getenv("APPID") ? getenv("APPID") : '',
        'sessionKey' => getenv("SESSION_KEY") ? getenv("SESSION_KEY") : ''
    ]
];