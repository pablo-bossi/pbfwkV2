<?php

/**
* This file is used to configure friendly urls to be handled on the fwk\Router class, mapping urls into controllers
* @example: array('pattern' => [regexp to match against the uri], 'handlerFile' => [Path to the class handling the request], 'handlerClass' => [Name of the controller class including the namespace], 'handlerMethod' => [Method to handle the request])
*/
$urlPatters = array();
$urlPatterns[] = array('pattern' => '/^\/hello\/([A-Z a-z]+)$/', 'method' => 'GET', 'handlerFile' => __DIR__.'/../controllers/main.php', 'handlerClass' => 'controllers\main', 'handlerMethod' => 'hello', 'extraParams' => array('name' => '$1'));
$urlPatterns[] = array('pattern' => '/^\/$/', 'method' => '*' ,'handlerFile' => __DIR__.'/../controllers/main.php', 'handlerClass' => 'controllers\main', 'handlerMethod' => 'index');


/**
* Files included in the array staticContentPaths will we rendered as they are without exceuting actions
*
*/
$staticContentPaths = array();
$staticContentPaths[] = '/templates/';
$staticContentPaths[] = '/js/';
$staticContentPaths[] = '/css/';
$staticContentPaths[] = '/images/';
$staticContentPaths[] = '/fonts/';
