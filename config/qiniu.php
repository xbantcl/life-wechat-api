<?php

return [
    'accessKey' => getenv("QINIU_ACCESSKEY") ? getenv("QINIU_ACCESSKEY") : '',
    'secretKey' => getenv("QINIU_SECRETKEY") ? getenv("QINIU_SECRETKEY") : '',
];