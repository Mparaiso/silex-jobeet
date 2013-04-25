<?php
$autoload = require __DIR__."/vendor/autoload.php";
$app = new App;
$app->boot();
$app['orm.console.boot_commands']();
$app['console']->run();