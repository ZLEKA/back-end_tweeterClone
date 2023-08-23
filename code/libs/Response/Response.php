<?php
header("Access-Control-Allow-Origin: http://localhost:9001");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST,DELETE");
class Response
{
    const HTTP_OK = 200;
    const HTTP_CREATED = 201;
    const HTTP_NO_CONTENT = 204;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_FORBIDDEN = 403;
    const HTTP_NOT_FOUND = 404;
    const HTTP_I_AM_A_TEAPOT = 418;
    const HTTP_INTERNAL_SERVER_ERROR = 500;
    const HTTP_BAD_GATEWAY = 502;

    const JSON_CONTENT = 'Content-type: application/json';
    const TEXT_CONTENT = 'Content-type: text/plain';

    /**
     * Return the desired HTTP code
     * @param int $status
     */
    public static function code(int $status=self::HTTP_OK){
        http_response_code($status);
    }

    /**
     * Return the desired HTTP code with json
     * @param int $status
     * @param mixed $content // Object|array is available from 7.2 :C
     * @param int|string $flags
     */
    public static function json($content=[],int $status=self::HTTP_OK,$flags=JSON_FORCE_OBJECT|JSON_NUMERIC_CHECK){
        header(self::JSON_CONTENT);
        http_response_code($status);
        echo json_encode($content,$flags);
    }

    /**
     * Return the desired HTTP code with text
     * @param int $status
     * @param string $content
     */
    public static function text(string $content='',int $status=self::HTTP_OK){
        header(self::TEXT_CONTENT);
        http_response_code($status);
        echo $content;
    }
}
