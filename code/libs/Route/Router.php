<?php

require_once __DIR__.'/RouteProviders.php';

const POST                          ='POST';
const GET                           ='GET';
const DELETE                        ='DELETE';
const PATCH                         ='PATCH';
CONST ROUTE_NOT_FOUND               ='Route not found';

class Router extends RouteProviders
{
    private $routes=[];

    /**
     * Handle the Request with the correct Route
     *
     * @param Request $request
     * @return void
     * @throws Exception
     */
    public function handle(Request $request)
    {
        $route = array_reduce($this->routes,function($chosen,$route) use ($request){
            if($route->match($request))
                $chosen=$route;
            return $chosen;
        });

        if($route instanceof Route)
            $route->run($request);
        else
            throw new Exception(ROUTE_NOT_FOUND);
    }

    /**
     * Create and push to routes the actual Route object
     * @param string $method
     * @param string $uri
     * @param $action
     * @throws Exception
     */
    private function register(string $method, string $uri, $action){
        array_push($this->routes,new Route($method,$uri,$action));
    }

    /**
     * Register a new GET route.
     *
     * @param string $uri
     * @param \Closure|array|string|null $action
     * @throws Exception
     */
    public function get(string $uri, $action = []){
        $this->register(GET,$uri,$action);
    }

    /**
     * Register a new POST route with the router.
     *
     * @param string $uri
     * @param \Closure|array|string|null $action
     * @throws Exception
     */
    public function post(string $uri, $action = []){
        $this->register(POST,$uri,$action);
    }

    /**
     * Register a new PATCH route with the router.
     *
     * @param string $uri
     * @param \Closure|array|string|null $action
     * @throws Exception
     */
    public function patch(string $uri, $action = []){
        $this->register(PATCH,$uri,$action);
    }

    /**
     * Register a new DELETE route with the router.
     *
     * @param string $uri
     * @param \Closure|array|string|null $action
     * @throws Exception
     */
    public function delete(string $uri, $action = []){
        $this->register(DELETE,$uri,$action);
    }

}