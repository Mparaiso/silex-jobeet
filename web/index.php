<?php

$autoload = require __DIR__."/../vendor/autoload.php";

$app = new App(array("debug"=>true));

$app['http_cache']->run();