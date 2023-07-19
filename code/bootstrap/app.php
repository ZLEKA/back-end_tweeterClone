<?php

require_once __DIR__ . '/../libs/Controller/Controller.php';
require_once __DIR__ . './../libs/Request/Request.php';
require_once __DIR__ . './../libs/Route/Router.php';
require_once __DIR__ . '/../libs/Env/Env.php';
require_once __DIR__ . '/../libs/Model/Model.php';
require_once __DIR__ . '/../libs/Tools/Tools.php';
require_once __DIR__ . '/../libs/View/View.php';
require_once __DIR__ . '/../libs/Response/Response.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
| Wake up the application by handling the incoming request
|
*/

try {

    /*Initializing the .env*/
    Env::setPath($_SERVER['DOCUMENT_ROOT']);

    Model::configConnection(
        Env::get('DB_HOST'),
        Env::get('DB_NAME'),
        Env::get('DB_USER'),
        Env::get('DB_PASSWORD')
    );


    /*Initializing the Router*/
    $route = new Router();

    /** Disable deprecation warnings */
    error_reporting( E_ALL ^ E_DEPRECATED );

    /*Handling the request*/
    $route->handle(
        Request::capture()
    );

} catch (\Exception $e) {
    print($e->getMessage());
}
