<?php

//Show all errors
error_reporting(E_ALL);
ini_set('display_errors', '1');

//This autoload path is for loading current version of phramework
//(must be removed when we move the examples to another repository)
require __DIR__ . '/../../../vendor/autoload.php';

require __DIR__ . '/../vendor/autoload.php';

//define controller namespace, as shortcut
define('NS', 'Examples\\JSONAPI\\APP\\Controllers\\');

use \Phramework\Phramework;

/**
 * @package examples/JSONAPI
 * Define APP as function
 */
$APP = function () {

    //Include settings
    $settings = include __DIR__ . '/../settings.php';

    $URIStrategy = new \Phramework\URIStrategy\URITemplate([
        ['test/', NS . 'TestController', 'GET', Phramework::METHOD_GET],
        ['test/', NS . 'TestController', 'POST', Phramework::METHOD_POST],
        ['test/{id}', NS . 'TestController', 'GETById', Phramework::METHOD_GET],
        ['test/{id}', NS . 'TestController', 'PATCH', Phramework::METHOD_PATCH],
        ['test/{id}', NS . 'TestController', 'DELETE', Phramework::METHOD_DELETE],
        ['test/{id}/relationships/{relationship}', NS . 'TestController', 'byIdRelationships', Phramework::METHOD_ANY],
    ]);

    //Initialize API
    $phramework = new Phramework($settings, $URIStrategy);

    unset($settings);

    $phramework->setViewerClass(
        \Phramework\Viewers\JSONAPI::class
    );

    //Execute API
    $phramework->invoke();
};

/**
 * Execute APP
 */
$APP();
