<?php

function isValidUser($id){
$pdo = pdoSqlConnect();
$query = "SELECT EXISTS(SELECT * FROM User WHERE UserId=?
                        AND IsDeleted='N') AS exist;";
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
    $query = "SELECT EXISTS(SELECT * FROM User WHERE Email= ?
                        AND IsDeleted='N' ) AS exist;";
    $st = $pdo->prepare($query);
//    $st->execute([$param,$param]);
    $st->execute([$email]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);
}

function isValidDeveloper($nickname,$reviewid){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM Review
                    JOIN Application
                    WHERE Review.ReviewId=?
                        AND Application.ApplicationId=Review.ApplicationId
                      AND Application.DevName=? AND Review.IsDeleted='N' ) AS exist;";
    $st = $pdo->prepare($query);
//    $st->execute([$param,$param]);
    $st->execute([$reviewid,$nickname]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);
}

function isValidUserReview($userid,$reviewid){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM Review
                    WHERE Review.ReviewId=?
                        AND Review.UserId=? AND Review.IsDeleted='N' ) AS exist;";
    $st = $pdo->prepare($query);
//    $st->execute([$param,$param]);
    $st->execute([$reviewid,$userid]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);
}


