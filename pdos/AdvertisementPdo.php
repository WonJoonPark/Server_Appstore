<?php

function advertisementlist(&$cursor){
    $pdo = pdoSqlConnect();
    $countquery="SELECT COUNT(*) as cnt FROM Application
                    WHERE isDeleted='N'";
    $st = $pdo->prepare($countquery);
//    $st->execute([$param,$param]);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $countres = $st->fetchAll();
    if(isset($cursor)==FALSE){ //첫 번째 조회일 때
        //광고 노출의 형평성과 중복광고 조회 방지를 위해
        $cursor=rand(1,$countres[0]['cnt']);
    }
    $st=null;
    if($cursor+9>$countres){$cursor=$cursor%$countres;} //데이터 개수 범위를 벗어날 수도 있음


    $initquery="SELECT AdvertisementApp.ThumbnailUrl,IconImage,Application.ApplicationId,ApplicationName,Price,InAppPurchase,
       Summary,Category,AdvertisementApp.Detail as DetailInfo FROM AdvertisementApp
        join Application
        WHERE AdvertisementApp.IsDeleted='N' AND Application.ApplicationId=AdvertisementApp.ApplicationId
        ORDER BY  FLOOR($cursor + (RAND() * 10)) Limit 6";
    $st = $pdo->prepare($initquery);
//    $st->execute([$param,$param]);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $firstres = $st->fetchAll();
    $st=null;

    //이미지 가져오기 위해
    for($i=0; $i<sizeof($firstres); $i=$i+1) {
        $secondquery = "SELECT ImageUrl as AppImages FROM AppImage
        WHERE IsDeleted='N' AND ApplicationId=? ORDER BY 'Order' asc";
        $st = $pdo->prepare($secondquery);
//    $st->execute([$param,$param]);
        $st->execute([$firstres[$i]['ApplicationId']]);
        $st->setFetchMode(PDO::FETCH_ASSOC);
        $secondres = $st->fetchAll();
        $firstres[$i]['ImageSet'] = $secondres;
        $st = null; $secondres=null; $secondquery=null;
    }
    $pdo = null;
    $cursor=$cursor+10;
    return $firstres;
}
