<?php
require 'function.php';

const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";

$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        case"changechart":{
            $jwt=$_SERVER["HTTP_JWT"];
            if(isValidHeader($jwt,JWT_SECRET_KEY)){ //올바른 유저의 접근임을 확인
                $data=getDataByJWToken($jwt, JWT_SECRET_KEY);
                if(isValidAdmin($data->id)){
                ChangeChart();
                $res->isSuccess=TRUE;
                $res->code=100;
                $res->message="카테고리 별 순위조정이 완료되었습니다.";
                echo json_encode($res,JSON_NUMERIC_CHECK);
                return;
                }
                $res->isSuccess=FALSE;
                $res->code=200;
                $res->message="관리자만 접근할 수 있는 항목 입니다";
                echo json_encode($res,JSON_NUMERIC_CHECK);
                return;
            }
            $res->isSuccess=FALSE;
            $res->code=200;
            $res->message="잘못된 접근입니다. 다시 로그인부터 진행해주세요";
            echo json_encode($res,JSON_NUMERIC_CHECK);
            return;
        }
    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}