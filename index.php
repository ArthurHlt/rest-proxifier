<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';
define("DEBUG", false);
if (DEBUG) {
    //we load a better Error handler ( bypass xdebug )
    $whoops = new Whoops\Run();
    $errorPage = new Whoops\Handler\PrettyPageHandler();

    $errorPage->setPageTitle("It's broken :'(");

    $whoops->pushHandler($errorPage);
    $whoops->register();
}
$sphring = new \Arthurh\Sphring\Sphring();
$sphring->loadContext();

$proxifier = $sphring->getBean('restProxifier');
$proxifier->proxify();