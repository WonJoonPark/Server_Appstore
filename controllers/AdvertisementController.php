<?php
require 'function.php';

const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";

$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        case "adverlist":{
            $cursor=$_GET['LastCursor'];
            $res->result=advertisementlist($cursor);
            if(empty($res)){
                $res->isSuccess=FALSE;
                $res->code=200;
                $res->message="조회된 항목이 없습니다.";
                echo json_encode($res,JSON_NUMERIC_CHECK);
            }
            $res->LastCursor=$cursor;
            $res->isSuccess=TRUE;
            $res->code=100;
            $res->message="광고 앱 리스트 검색이 완료되었습니다.";
            echo json_encode($res,JSON_NUMERIC_CHECK);
            break;}
        }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}