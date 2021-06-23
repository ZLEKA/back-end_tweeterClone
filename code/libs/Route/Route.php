<?php

/*Errors*/
const ERROR_ACTION_NOT_VALID            = 'Route action not valid';
const ERROR_ACTION_USE_NOT_FOUND        = 'Route action does not contain the [use] key';
const ERROR_CONTROLLER_USE_NOT_CORRECT  = 'Something wrong with the [use] format, missing @ separator';
const ERROR_METHOD_DOES_NOT_EXISTS      = 'Method does not exists';
/*Misc*/
const ARRAY_USE_KEY                     = 'use';
const ARRAY_USE_SEPARATOR               = '@';
const URI_TOKEN_SEPARATOR               = '/';
const URI_REGEX_TOKEN_MATCH             = '/{(.*?)}/i';

class Route
{

    /**
     * The Route uri
     * @var string
     */
    protected $uri;

    /**
     * The Route Method
     * @var string
     */
    protected $method;

    /**
     * The route Action
     * @var array|Closure|string
     */
    protected $action;

    /**
     * The route tokenized params
     * @var array
     */
    protected $params=[];

    /**
     * @overridden in RouteProviders
     * @param $method
     * @param $uri
     * @param array|string|Closure $action
     * @throws Exception
     */
    public function __construct(string $method, string $uri,$action){
        $this->setAction($action);
        $this->method = $method;
        $this->uri = $uri;
    }

    /**
     * Validate and assign the Action
     * @param array|string|Closure $action
     * @return array|Closure|string
     * @throws Exception
     */
    private function setAction($action)
    {
        if (!$action instanceof Closure && !is_array($action) && !is_string($action))
            throw new Exception(ERROR_ACTION_NOT_VALID);

        if(is_array($action)){
            if (!array_key_exists(ARRAY_USE_KEY, $action))
                throw new Exception(ERROR_ACTION_USE_NOT_FOUND);

            if(!strpos($action[ARRAY_USE_KEY], ARRAY_USE_SEPARATOR))
                throw new Exception(ERROR_CONTROLLER_USE_NOT_CORRECT);
        }

            return $this->action=$action;
    }

    /**
     * Execute the Method in the specified Controller
     * @param array $action
     * @throws ReflectionException
     * @throws Exception
     */
    private function controller(array $action){

        list($controller, $method) = explode(ARRAY_USE_SEPARATOR, $action[ARRAY_USE_KEY]);

        $controller = Controller::make($controller);

        Controller::invoke($controller,$method,$this->params);
    }

    /**
     * Match the URIs while searching for white cards params.
     * @param Request $request
     * @return bool
     */
    private function matchTokens(Request $request):bool{
        $requestedUri = self::tokenize($request->uri);
        $definedUri =  self::tokenize($this->uri);

        $discoveredTokens = array_filter($requestedUri,function($v,$k) use ($definedUri){
            if(isset($definedUri[$k])&&$v==$definedUri[$k]) /*Token exists and is the same*/
                return true;
            elseif($v!=$definedUri[$k] && !preg_match(URI_REGEX_TOKEN_MATCH,$definedUri[$k])) /*Token is different but does not match the white card syntax*/
                return false;
            return $this->addParam($v); /*Save the params in found order for white card token*/
        },ARRAY_FILTER_USE_BOTH );

        /*Migrate from Bitbucket missing check*/

        $tReq = count($requestedUri);
        $tFound = count($discoveredTokens);
        $match = $tReq == $tFound;

        return ! $tReq ? $match && $request->uri[0]==$this->uri : $match;

    }
    /**
     * Run the Route action.
     * @param Request $request
     * @throws Exception
     */
    public function run(Request $request){
        try {

            array_unshift($this->params,$request);

            if ($this->action instanceof Closure)
                $this->closure($this->action);

            if (is_array($this->action))
                $this->controller($this->action);

            if (is_string($this->action))
                print($this->action);

        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * True if the passed request match this Route.
     * Short circuit will help performance since will break in method if not the one
     * @param Request $request
     * @return bool
     */
    public function match(Request $request):bool{
        return $this->matchMethod($request) && $this->matchTokens($request);
    }

    /**
     * Sanitize and return a tokenized array of the passed URI (String)
     * Empty array is returned if uri is empty
     * @param string $uri
     * @return array
     */
    private static function tokenize(string $uri):array{
        return strlen($uri) ? array_filter(explode(URI_TOKEN_SEPARATOR,$uri),'strlen') : [];
    }
    /**
     * Execute the Closure
     * @param Closure $fn
     */
    private function closure(Closure $fn){
        call_user_func_array($fn,$this->params);
    }

    /**
     * @param $param
     * @return bool
     */
    private function addParam($param):bool{
        return array_push($this->params,$param);
    }
    /**
     * Check if the method is same as request
     * @param Request $request
     * @return bool
     */
    private function matchMethod(Request $request):bool{
        return $request->method==$this->method;
    }




}