<?php

function searchapps($keyw, $dev){
    $pdo = pdoSqlConnect();
    //기본 설정은 인기가 많은 순위
    $initquery="SELECT IconImage,ApplicationId,ApplicationName,Price,InAppPurchase,Summary FROM Application
            WHERE isDeleted='N'";
    switch ($keyw){
        case "popularity":{
            $query=$initquery."ORDER BY WeekDownCount desc LIMIT 15";
            break;}
        case "popularityfinance":{
            $query=$initquery."AND Category='금융' ORDER BY WeekDownCount desc LIMIT 15;";
            break;}
        case "newupdate":{
            $query=$initquery."AND date(UpdateAt) >=date(subdate(now(),INTERVAL 3 DAY )) and date(UpdateAt) <= date(now()) ORDER BY UpdateAt desc LIMIT 15;";
            break;}
        case "dev":{
            $query=$initquery."AND DevName='$dev' ORDER BY CreateAt desc LIMIT 15;";
            break;}
        case "devrecent":{
            $query=$initquery."AND DevName='$dev' AND date(CreateAt) >=date(subdate(now(),INTERVAL 30 DAY )) and date(CreateAt) <= date(now())
            ORDER BY CreateAt desc LIMIT 15;";
        }
    }
    $st = $pdo->prepare($query);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res;
}
function isValidAppId($appid){
    $pdo = pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM Application WHERE ApplicationId= ?
                        AND IsDeleted='N') AS exist;";
    $st = $pdo->prepare($query);
//    $st->execute([$param,$param]);
    $st->execute([$appid]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st=null;$pdo = null;
    return intval($res[0]["exist"]);
}
function AppSpecification($appid){
    $pdo = pdoSqlConnect();
    $firstquery="SELECT IconImage,ApplicationId,ApplicationName,Price,Summary,InAppPurchase,
       Ages,Chart,DevName,DetailInfo,Appsize,Category,Compatibility,Word,WordCount,WordDetail,Copyright FROM Application
        WHERE IsDeleted='N' AND ApplicationId=?";
    $st = $pdo->prepare($firstquery);
//    $st->execute([$param,$param]);
    $st->execute([$appid]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $firstres = $st->fetchAll();
    $st=null;$pdo = null;

    $pdo = pdoSqlConnect();
    $secondquery="SELECT ImageUrl as AppImages FROM AppImage
        WHERE IsDeleted='N' AND ApplicationId=? ORDER BY 'Order' asc";
    $st = $pdo->prepare($secondquery);
//    $st->execute([$param,$param]);
    $st->execute([$appid]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $secondres=$st->fetchAll();
    $firstres[0]['ImageSet']=$secondres;
    $st=null;$pdo = null;

    return $firstres[0];
}


