<?php

$router->get('/', function () use ($router) {
    return $router->current()->action;
})->name('index');
