<?php

function ChangeChart(){
    $pdo = pdoSqlConnect();
    $groupquery = "SELECT Category FROM Application
                    GROUP BY Category;";
    $st = $pdo->prepare($groupquery);
//    $st->execute([$param,$param]);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $groupres = $st->fetchAll();
    $st=null;
    for($i=0; $i<sizeof($groupres); $i=$i+1){
        $chartquery = "SELECT Application.ApplicationId,ApplicationName,Purchase.UserId,ApplicationName,(EvaluationSum/EvaluationCount) as Eva,
       (100 * count( if((date(Purchase.CreateAt) >= DATE_SUB(now(), INTERVAL 7 DAY)),Purchase.CreateAt,null) )
       +10 * count( if(date(Purchase.CreateAt) < DATE_SUB(now(), INTERVAL 7 DAY) AND date(Purchase.CreateAt) >= DATE_SUB(now(), INTERVAL 1 MONTH ),Purchase.CreateAt,null))
        +1 * count( if(date(Purchase.CreateAt) < DATE_SUB(now(), INTERVAL 1 MONTH) AND date(Purchase.CreateAt) >= DATE_SUB(now(), INTERVAL 6 MONTH ),Purchase.CreateAt,null))
        ) as Score
            FROM Application
            LEFT OUTER JOIN Purchase ON Application.ApplicationId=Purchase.ApplicationId WHERE Application.Category=? AND Application.IsDeleted='N'
    GROUP BY Application.ApplicationId ORDER BY Score desc, Eva desc;";
        $st = $pdo->prepare($chartquery);
//    $st->execute([$param,$param]);
        $st->execute([$groupres[$i]['Category']]);
        $st->setFetchMode(PDO::FETCH_ASSOC);
        $chartres = $st->fetchAll();
        $st=null;
        for($j=1;$j<=sizeof($chartres);$j=$j+1){
            $insertquery="UPDATE Application SET Chart=? WHERE ApplicationId=?";
            $st = $pdo->prepare($insertquery);
//    $st->execute([$param,$param]);
            $st->execute([$j,$chartres[$j-1]['ApplicationId']]);
            $st=null;
        }
        $chartres=null;
    }
    $groupres=null; $pdo=null;
}