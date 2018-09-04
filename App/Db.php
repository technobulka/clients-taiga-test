<?php

namespace App;

class Db extends \mysqli
{
    public function __construct()
    {
        $config = include('App/config/app.php');
        extract($config['db']);
        parent::__construct($host, $username, $password, $dbname);
    }

    public function __destruct()
    {
        parent::close();
    }
}