<?php

declare(strict_types=1);

/*
include - load file and gives warning if missing
require - load file and throw error if missing
require_once / include_once - not load file if it was already loaded
*/

require __DIR__ . "/../../vendor/autoload.php";

# TODO composer nie ładuje tych plików chociarz ma je wskazane w files, bez dodaktowego importu są niewidoczne
include __DIR__ . "/Config/Middleware.php";
include __DIR__ . "/Config/Routes.php";

use function App\Config\registerMiddleware;
use function App\Config\registerRoutes;
use App\Config\Paths;
use Framework\App;
use App\Controllers\{HomeController, AboutController};


$app = new App(Paths::SOURCE . "App/container-definitions.php");

registerRoutes($app);
registerMiddleware($app);

return $app;
