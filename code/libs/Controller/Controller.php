<?php

const PHP = '.php';
const ERROR_CONTROLLER_NOT_FOUND    = 'Controller does not exists';
const ERROR_METHOD_NOT_PUBLIC       = 'Method is not public';

class Controller
{
    /**
     * Find the Controller File and search for the Controller class
     * @param $class
     * @return mixed
     * @throws Exception
     */

    
    public static function make($class): ReflectionClass{
        if (!self::exists($class))
            throw new Exception(ERROR_CONTROLLER_NOT_FOUND);

        require_once Env::app('CONTROLLERS') . $class . PHP;

        return new ReflectionClass($class);
    }

    /**
     * Validate and Invoke the Controllers Method with the desired params
     * The Method should be Public.
     * @param ReflectionClass $controller
     * @param $method
     * @param array $params
     * @throws ReflectionException
     * @throws Exception
     */
    public static function invoke(ReflectionClass $controller, $method, array $params){
        if (!$controller->hasMethod($method))
            throw new Exception(ERROR_METHOD_DOES_NOT_EXISTS);

        if(!$controller->getMethod($method)->isPublic())
            throw new Exception(ERROR_METHOD_NOT_PUBLIC);

        $controller->getMethod($method)->invoke($controller->newInstance(),...$params);
    }

    /**
     * Check if the controller exists
     * @param $controller
     * @return bool
     */
    public static function exists($controller): bool{
        return in_array($controller . PHP, scandir(Env::app('CONTROLLERS')));
    }


    /**
     * Writes a rendered view into response
     * @param string $view
     * @param array $data
     * @return View
     * @throws Exception
     */
    public function view(string $view, array $data = []): View {
        return new View($view, $data);
    }

    /**
     * Writes a plain test response
     * @param string $content
     * @param int $status
     * @return void
     */
    public function response(string $content, int $status = Response::HTTP_OK): void {
        Response::text($content, $status);
    }

    /**
     * Writes a JSON encoded response
     * @param array $data
     * @param int $status
     * @return void
     */
    public function jsonResponse(array $data, int $status = Response::HTTP_OK): void {
        Response::json($data, $status);
    }

}