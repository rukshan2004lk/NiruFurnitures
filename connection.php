<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

class Database
{
    public static $connection;

    public static function setUpConnection()
    {
        if (!isset(Database::$connection)) {

            Database::$connection = new mysqli(
                "localhost",
                "root",
                "Rukshan123",
                "niru_furniture",
                3306
            );

            if (Database::$connection->connect_error) {
                die("Database Connection Failed: " . Database::$connection->connect_error);
            }

            echo "Database Connection Successful";
        }
    }

    public static function iud($q)
    {
        Database::setUpConnection();
        return Database::$connection->query($q);
    }

    public static function search($q)
    {
        Database::setUpConnection();
        return Database::$connection->query($q);
    }
}

Database::setUpConnection();

?>