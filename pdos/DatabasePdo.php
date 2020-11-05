<?php
//DB 정보
function pdoSqlConnect()
{
    try {
        $DB_HOST = "database-1.cc2lvcxkdpmy.ap-northeast-2.rds.amazonaws.com";
        $DB_NAME = "appstore_sql";
        $DB_USER = "park";
        $DB_PW = "parkdb123";

        $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME", $DB_USER, $DB_PW);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("set names utf8");
        return $pdo;
    } catch (\Exception $e) {
        echo $e->getMessage();
    }
}