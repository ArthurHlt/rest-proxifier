<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';
$sphring = new \Arthurh\Sphring\Sphring();
$sphring->loadContext();

$proxifier = $sphring->getBean('restProxifier');
$proxifier->proxify();