<?php
/**
 * FR : application console , lancer avec php console.php
 * EN : console application , launch with php console.php
 */
$autoload = require __DIR__."/vendor/autoload.php";
$app = new App(array("debug"=>TRUE));
$app->boot();
$app['orm.console.boot_commands']();
$app['console']->run();