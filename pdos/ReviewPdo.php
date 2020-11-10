<?php

function SearchReview($appid,$order){
    $pdo = pdoSqlConnect();
    //기본 설정은 인기가 많은 순위
    $initquery="SELECT ReviewId,Title,Stars,NickName,Comment as Review,CreateAt as ReviewAt,Answer,AnswerAt FROM Review
WHERE IsDeleted='N' AND ApplicationId=?";
    switch ($order){
        case "recent":{
            $query=$initquery."ORDER BY ReviewAt desc";
            break;
        }
        case "favorable":{
            $query=$initquery."ORDER BY Stars desc, ReviewAt desc";
            break;
        }
        case "critic":{
            $query=$initquery."ORDER BY Stars asc, ReviewAt desc";
            break;
        }
        default:{ //helpful
            $query=$initquery." ANd Answer IS NOT NULL
                                ORDER BY LENGTH(Review) desc,ReviewAt desc ";
            break;
        }
    }
    $st = $pdo->prepare($query);
    $st->execute([$appid]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res;
}

function InsertReview($data,$appid,$title,$comment,$stars){
    $pdo = pdoSqlConnect();
    $name=$data->nickname;
    $userid=$data->id;
    $firstquery="insert into Review value(default,?,?,?,?,?,?,now(),default,default,default,default);";
    $st = $pdo->prepare($firstquery);
    $st->execute([$appid,$stars,$title,$comment,$userid,$name]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $st = null;

    $secondquery="UPDATE Application SET EvaluationCount=EvaluationCount+1,EvaluationSum=EvaluationSum+?
                    WHERE ApplicationId=?;";
    $st = $pdo->prepare($secondquery);
    $st->execute([$stars,$appid]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $st = null;

    $pdo = null;
}

function InsertAnswer($reviewid,$answer){
    $pdo = pdoSqlConnect();
    $secondquery="UPDATE Review SET Answer=?, AnswerAt=now()
                    WHERE ReviewId=?;";
    $st = $pdo->prepare($secondquery);
    $st->execute([$answer,$reviewid]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $st = null;
    $pdo = null;
}

function delreview($reviewid){
    $pdo = pdoSqlConnect();
    $secondquery="UPDATE Review SET IsDeleted='Y'
                    WHERE ReviewId=?;";
    $st = $pdo->prepare($secondquery);
    $st->execute([$reviewid]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $st = null;
    $pdo = null;
}