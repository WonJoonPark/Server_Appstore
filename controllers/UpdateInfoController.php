<?php
require 'function.php';

const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";

$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        case "updatelist":{
            $appid=$_GET['appid'];
            if(isset($appid)==FALSE){
                $res->isSuccess=FALSE;
                $res->code=200;
                $res->message="ApplicationId 를 입력해주세요";
                echo json_encode($res,JSON_NUMERIC_CHECK);
                break;
            }
            $res->result=updatelist($appid);
            if(empty($res)){
                $res->isSuccess=FALSE;
                $res->code=200;
                $res->message="업데이트 항목이 없습니다.";
                echo json_encode($res,JSON_NUMERIC_CHECK);
                break;
            }
            $res->isSuccess=TRUE;
            $res->code=100;
            $res->message="업데이트 리스트 검색이 완료되었습니다.";
            echo json_encode($res,JSON_NUMERIC_CHECK);
            break;
           }
    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}