<?php

function insertkakaouser($id,$email,$nickname){
    $pdo=pdoSqlConnect();
    $query = "INSERT INTO User(UserId,Email,CreateAt,NickName)
              VALUES (?,?,now(),?);";
    $st = $pdo->prepare($query);
    $st->execute([$id,$email,$nickname]);
    $st = null;
    $pdo = null;

}
function resetuser($userid){
    $pdo = pdoSqlConnect();
    $query="UPDATE User SET IsDeleted='N',CreateAt=now()
                    WHERE UserId=?;";
    $st = $pdo->prepare($query);
    $st->execute([$userid]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $st = null;
    $pdo = null;
}

function UserPurchase($data,$appid){
    $userid=$data->id;
    $pdo=pdoSqlConnect();
    $firstquery="SELECT ApplicationId,ApplicationName,Price,PorS FROM Application
                    WHERE isDeleted='N' AND ApplicationId=?;";
    $st = $pdo->prepare($firstquery);
//    $st->execute([$param,$param]);
    $st->execute([$appid]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $firstres = $st->fetchAll();
    $st=null;
    if($firstres[0]['PorS']=='P'){ //구매일경우
    $secondquery="INSERT INTO Purchase(UserId,ApplicationId,Price,CreateAt)
              VALUES (?,?,?,now());";
        $st = $pdo->prepare($secondquery);
        $st->execute([$userid,$firstres[0]['ApplicationId'],$firstres[0]['Price']]);
        $st = null;
        $pdo = null;
    }
    else{ //구독일 경우
        $secondquery="INSERT INTO Purchase(UserId,ApplicationId,Price,CreateAt,ModifyAt,PorS)
              VALUES (?,?,now(),DATE_ADD(now(),INTERVAL 1 MONTH),?,?);";
        $st = $pdo->prepare($secondquery);
        $st->execute([$userid,$firstres[0]['ApplicationId'],$firstres[0]['Price'],$firstres[0]['PorS']]);
        $st = null;
        $pdo = null;
    }
    unset($firstres[0]['Price']);
    unset($firstres[0]['PorS']);

    return $firstres[0];
}

function UserDownloadSearch($data,$appid){
    $userid=$data->id;
    $pdo=pdoSqlConnect();
    $query = "SELECT EXISTS(SELECT * FROM Purchase WHERE ApplicationId= ?
                       AND UserId=? AND IsDeleted='N' ) AS exist;";
    $st = $pdo->prepare($query);
//    $st->execute([$param,$param]);
    $st->execute([$appid,$userid]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $st=null;$pdo = null;

    return intval($res[0]["exist"]);
}
function PurchaseList($data,$kind,$pagenum){
    $userid=$data->id;
    $pagenum=12*($pagenum-1);
    $pdo=pdoSqlConnect();
    if($kind=='purchase'){$k='P';}
    else{$k='S';}

    $firstquery = "SELECT ApplicationId,CreateAt as DownloadAt,ModifyAt FROM Purchase
                WHERE IsDeleted='N' AND UserId=? AND PorS=? LIMIT $pagenum,12;";
    $st = $pdo->prepare($firstquery);
    $st->execute([$userid,$k]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $firstres = $st->fetchAll();
    $st=null;

    $secondquery="SELECT IconImage,ApplicationName FROM Application
        WHERE ApplicationId=? AND IsDeleted='N';";
    for($i=0; $i<sizeof($firstres); $i=$i+1){
        $st = $pdo->prepare($secondquery);
        $st->execute([$firstres[$i]['ApplicationId']]);
        $st->setFetchMode(PDO::FETCH_ASSOC);
        $secondres = $st->fetchAll();
        $firstres[$i]['IconImage']=$secondres[0]['IconImage'];
        $firstres[$i]['ApplicationName']=$secondres[0]['ApplicationName'];
        $secondres=null;
        $st=null;
    }
    $pdo=null;
    return $firstres;
}
function DeleteUser($userid){
    $pdo = pdoSqlConnect();
    $secondquery="UPDATE User SET IsDeleted='Y'
                    WHERE UserId=?;";
    $st = $pdo->prepare($secondquery);
    $st->execute([$userid]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $st = null;
    $pdo = null;
}