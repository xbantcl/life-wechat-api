<?php
// Set Middleware
use Dolphin\Ting\Bootstrap\Middleware\CORSMiddleware;
use Dolphin\Ting\Bootstrap\Middleware\AuthMiddleware;

return function ($app) {
    // Body Parsing Middleware
    $app->addBodyParsingMiddleware();
    // Route Middleware
    $app->addRoutingMiddleware();
    // Cross Origin Resource Sharing Middleware
    $app->add(new CORSMiddleware());
};
