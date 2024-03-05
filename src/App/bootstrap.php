<?php

declare(strict_types=1);

/*
include - load file and gives warning if missing
require - load file and throw error if missing
require_once / include_once - not load file if it was already loaded
*/

require __DIR__ . "/../../vendor/autoload.php";

use App\Config\Paths;
use Framework\App;
use App\Controllers\{HomeController, AboutController};
use Dotenv\Dotenv;
use function App\Config\{registerMiddleware, registerRoutes};

$dotenv = Dotenv::createImmutable(Paths::ROOT);
$dotenv->load();

$app = new App(Paths::SOURCE . "App/container-definitions.php");

registerRoutes($app);
registerMiddleware($app);

return $app;
