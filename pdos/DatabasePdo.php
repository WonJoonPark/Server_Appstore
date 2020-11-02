<?php
//DB 정보
function pdoSqlConnect()
{
    try {
        $DB_HOST = "13.125.53.4";
        $DB_NAME = "mysql";
        $DB_USER = "root";
        $DB_PW = "tifjq94101@";

        $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME", $DB_USER, $DB_PW);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("set names utf8");
        return $pdo;
    } catch (\Exception $e) {
        echo $e->getMessage();
    }
}