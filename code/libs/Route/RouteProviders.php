<?php

require_once __DIR__.'/Route.php';

class RouteProviders extends Route
{

    /**
     * @override
     * RouteProviders constructor.
     */
    public function __construct()
    {
        $this->parse(function (){
            require Env::app('HTTP').'routes.php';
        });
    }

    /**
     * @param Closure $callback
     */
    private function parse(Closure $callback){
        call_user_func($callback);
    }
}