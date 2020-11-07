<?php

function updatelist($appid){
    $pdo = pdoSqlConnect();
    $query="SELECT UpdateVer,TIMESTAMPDIFF(DAY,CreateAt,now()) as UpdateTime,Contents FROM UpdateInfo
            WHERE IsDeleted='N' AND ApplicationId=? order by CreateAt desc;";
    $st = $pdo->prepare($query);
    $st->execute([$appid]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res;
}