<?php
    $env = parse_ini_file(__DIR__ . "/../../../.env");

    function connect() {
        global $env;
        $serverName = $env['SERVER_NAME'];
        $username = $env['USERNAME'];
        $password = $env['PASSWORD'];
        $dbName = $env['DBNAME'];

        $conn = new mysqli($serverName, $username, $password, $dbName);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        return $conn;
    }