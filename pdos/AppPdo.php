<?php

function searchapps($keyw, $dev,$pagenum){
    $pdo = pdoSqlConnect();
    //기본 설정은 인기가 많은 순위
    $initquery="SELECT IconImage,ApplicationId,ApplicationName,Price,InAppPurchase,Summary FROM Application
            WHERE isDeleted='N'";
    $pagecursor=12*($pagenum-1);
    switch ($keyw){
        case "popularity":{
            $query=$initquery."ORDER BY WeekDownCount desc LIMIT $pagecursor,12";
            break;}
        case "popularityfinance":{
            $query=$initquery."AND Category='금융' ORDER BY WeekDownCount desc LIMIT $pagecursor,12;";
            break;}
        case "newupdate":{
            $query=$initquery."AND date(UpdateAt) >=date(subdate(now(),INTERVAL 3 DAY )) and date(UpdateAt) <= date(now()) ORDER BY UpdateAt desc LIMIT $pagecursor,12;";
            break;}
        case "dev":{
            $query=$initquery."AND DevName='$dev' ORDER BY CreateAt desc LIMIT $pagecursor,12;";
            break;}
        case "devrecent":{
            $query=$initquery."AND DevName='$dev' AND date(CreateAt) >=date(subdate(now(),INTERVAL 30 DAY )) and date(CreateAt) <= date(now())
            ORDER BY CreateAt desc LIMIT $pagecursor,12;";
            break;
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

function SearchAppList($word,$pagenum){
    $pdo = pdoSqlConnect();
    $pagecursor=12*($pagenum-1);
    $firstquery="SELECT IconImage,ApplicationId,ApplicationName,Summary,Price,InAppPurchase,
       (100000*(ApplicationName LIKE '%$word%')+100000*(DevName LIKE '%$word%')+
       10000*(Category LIKE '%$word%')+1000*(Summary LIKE '%$word%'))+(1000-Chart) as score FROM Application
WHERE IsDeleted='N' AND (ApplicationName LIKE '%$word%' OR (DevName LIKE '%$word%')
  OR (Category LIKE '%$word%') OR (Summary LIKE '%$word%')) ORDER BY score desc LIMIT $pagecursor,12";
    $st = $pdo->prepare($firstquery);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $firstres = $st->fetchAll();
    $st = null;

    $secondquery = "SELECT ImageUrl as AppImages FROM AppImage
        WHERE IsDeleted='N' AND ApplicationId=? ORDER BY 'Order' asc";

    for($i=0; $i<sizeof($firstres); $i=$i+1) {
        $st = $pdo->prepare($secondquery);
//    $st->execute([$param,$param]);
        $st->execute([$firstres[$i]['ApplicationId']]);
        $st->setFetchMode(PDO::FETCH_ASSOC);
        $secondres = $st->fetchAll();
        $firstres[$i]['ImageSet'] = $secondres;
        $st = null; $secondres=null;
        unset($firstres[$i]['score']);
    }
    $pdo = null;

    return $firstres;
}
