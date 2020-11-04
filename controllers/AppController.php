<?php
require 'function.php';

const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";

$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        case "applist":{
            $keyw=$_GET['keyword'];
            $devname=$_GET['devname'];
            if($keyw=="dev" && isset($devname)==FALSE){
                    $res->isSuccess=FALSE;
                    $res->code=200;
                    echo json_encode($res,JSON_NUMERIC_CHECK);
                    break;
            }
            http_response_code(200);
            $res->result=searchapps($keyw,$devname);
            $res->isSuccess=TRUE;
            $res->code=100;
            echo json_encode($res,JSON_NUMERIC_CHECK);
            break;
        }
        case "searchapp":{
            $searchword=$_GET['word'];
            if(isset($searchword)==FALSE){
                $res->isSuccess=FALSE;
                $res->code=200;
                $res->message="검색어를 입력해주세요";
                echo json_encode($res,JSON_NUMERIC_CHECK);
                break;
            }
            http_response_code(200);
            $res->result=searchappsword($searchword);
            $res->isSuccess=TRUE;
            $res->code=100;
            $res->message="검색 완료";
            echo json_encode($res,JSON_NUMERIC_CHECK);
            break;
        }
    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}