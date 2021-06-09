<?php

require_once __DIR__.'/Parameter.php';

class Request
{
    /**
     * Request method
     *
     */
    public $method;

    /**
     * Custom parameters.
     *
     */
    public $attributes;

    /**
     * Request body parameters ($_POST).
     */
    public $request;

    /**
     * Query string parameters ($_GET).
     *
     */
    public $query;

    /**
     * Server and execution environment parameters ($_SERVER).
     *
     */
    public $server;

    /**
     * Uploaded files ($_FILES).
     *
     */
    public $files;

    /**
     * Cookies ($_COOKIE).
     *
     */
    public $cookies;

    /**
     * Headers (taken from the $_SERVER).
     *
     */
    public $headers;

    /**
     * The raw Body data
     *
     */
    public $content;

    /**
     * The URI
     *
     */
    public $uri;

    /**
     * Constructor.
     *
     * @param array           $query      The GET parameters
     * @param array           $request    The POST parameters
     * @param array           $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
     * @param array           $cookies    The COOKIE parameters
     * @param array           $files      The FILES parameters
     * @param array           $server     The SERVER parameters
     * @param string|resource $content    The raw body data
     */
    public function __construct(array $query = array(), array $request = array(), array $attributes = array(), array $cookies = array(), array $files = array(), array $server = array(), $content = null){
        $this->initialize($query, $request, $attributes, $cookies, $files, $server, $content);
    }

    /**
     * Capture the current request.
     * @return Request
     */
    public static function capture(): Request{
         return new static($_GET, $_POST, array(), $_COOKIE, $_FILES, $_SERVER,file_get_contents('php://input'));
    }


    /**
     * Initialize all the Parameters received from the request
     * @param array $query
     * @param array $request
     * @param array $attributes
     * @param array $cookies
     * @param array $files
     * @param array $server
     * @param null $content
     */
    public function initialize(array $query = array(), array $request = array(), array $attributes = array(), array $cookies = array(), array $files = array(), array $server = array(), $content = null){
        $this->request = new Parameter($request);
        $this->query = new Parameter($query);
        $this->attributes = new Parameter($attributes);
        $this->cookies = new Parameter($cookies);
        $this->files = new Parameter($files);
        $this->server = new Parameter($server);
        $this->headers = new Parameter($this->server->all());
        $this->method = $this->method();
        $this->content = $content;
        $this->uri = $this->uri();
    }

    /**
     * The the request method.
     * Fallback to GET method.
     * @return string
     */
    private function method(): string{
        return strtoupper($this->server->get('REQUEST_METHOD', 'GET'));
    }

    /**
     * Get the original request URI without the query parameters.
     * @return string
     */
    private function uri(): string{
        return strtok($this->server->get('REQUEST_URI'), '?');
    }
}