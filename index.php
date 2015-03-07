<?php
require_once __DIR__ . '/vendor/autoload.php';
$proxifier = \Arthurh\RestProxifier\RestProxifier::getInstance(__DIR__ . '/config');
$proxifier->proxify();