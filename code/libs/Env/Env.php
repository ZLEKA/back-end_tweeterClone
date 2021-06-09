<?php

class Env
{
    const VAR_SEPARATOR = '=';
    const DEFAULT_FILENAME = '.env';

    private static $env;
    private static $envPath;
    private $vars;

    private function __construct()
    {
        $this->vars = [];

        if (!$this->loadDotEnv(self::$envPath ?? '')) {
            throw new \Exception("Dotenv cannot  be loaded");
        }
    }

    private function read($path) {

        if ($path == null || !file_exists($path) || is_dir($path)) {
            throw new \Exception("Could not open $path");
        }

        $file = fopen($path, 'r');

        if ($file === false) {
            throw new \Exception('Could not open file');
        }

        while ($line = fgets($file)) { yield $line; }

        fclose($file);
    }

    private function parseDotenv(string $path) {
        $vars = [];

        foreach ($this->read($path) as $line) {
            $pair = explode(self::VAR_SEPARATOR, $line, 2);

            // Skip invalid pairs
            if (count($pair) < 2) {
                continue;
            }

            $key = trim($pair[0]);
            $val = trim($pair[1]);

            $vars[$key] = $val;
        }
        return $vars;
    }

    private function loadDotEnv(string $path = '') {
        if (!empty($path) && (!file_exists($path) || !is_dir($path))) {
            return false;
        }

        $files = glob(self::DEFAULT_FILENAME);

        if (empty($files)) {
            return false;
        }

        $this->vars = $this->parseDotenv($files[0]);

        return true;
    }

    private static function initIfNeeded() {
        if (self::$env == null) {
            self::$env = new Env();
        }
    }

    public static function get(string $key) {
        self::initIfNeeded();
        return self::$env->vars[$key] ?? null;
    }

    public static function all(): array {
        self::initIfNeeded();
        return self::$env->vars;
    }

    public static function exist($key): bool {
        self::initIfNeeded();
        return isset(self::$env->vars[$key]);
    }

    public static function setPath($path) {
        self::$envPath = $path;
        self::$env = null;
    }

    public static function app(string $key){
        return  self::$envPath.'/app/'.self::get($key).'/';
    }

}
