<?php

function isValidUser($id, $pw){
$pdo = pdoSqlConnect();
$query = "SELECT EXISTS(SELECT * FROM membership WHERE Email= ? AND Password = ?
                        AND IsDeleted='N') AS exist;";


$st = $pdo->prepare($query);
//    $st->execute([$param,$param]);
$st->execute([$id, $pw]);
$st->setFetchMode(PDO::FETCH_ASSOC);
$res = $st->fetchAll();

$st=null;$pdo = null;


return intval($res[0]["exist"]);

}
function isValidkakao($id){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM membership WHERE UserId= ?
                        AND IsDeleted='N' AND Platform='k' ) AS exist;";


    $st = $pdo->prepare($query);
//    $st->execute([$param,$param]);
    $st->execute([$id]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;


    return intval($res[0]["exist"]);
}
function isalreadykakao($email){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM membership WHERE Email= ?
                        AND IsDeleted='N' AND Platform='k' ) AS exist;";


    $st = $pdo->prepare($query);
//    $st->execute([$param,$param]);
    $st->execute([$email]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;


    return intval($res[0]["exist"]);
}
function insertkakaouser($id,$email,$nickname){
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO membership VALUE (?,default,default,default,?,'***','010',0
                            ,default,?,default,default,default,default,default,default,default
                             ,default,default,'k') ;";
    $st = $pdo->prepare($query);
//    $st->execute([$param,$param]);
    $st->execute([$nickname,$email,$id]);
    $st=null;$pdo = null;
}

