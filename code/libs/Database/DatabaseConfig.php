<?php


class DatabaseConfig
{
    public $host;
    public $user;
    public $pass;
    public $database;

    public function __construct(string $host, string $database, string $user, string $pass)
    {
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->database = $database;
    }
}
