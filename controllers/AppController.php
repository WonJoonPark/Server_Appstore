<?php
require 'function.php';

const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";

$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        case"applist":{
            $keyw=$_GET['keyword'];
            $devname=$_GET['devname'];
            $pagenum=$_GET['pagenum'];
            if($keyw=="dev" && isset($devname)==FALSE){
                $res->result=NULL;
                $res->isSuccess=FALSE;
                $res->code=200;
                $res->message="개발자 명을 입력해주세요.";
                echo json_encode($res,JSON_NUMERIC_CHECK);
                break;
            }
            $res->result=searchapps($keyw,$devname,$pagenum);
            if(empty($res->result)){
                $res->result=NULL;
                $res->isSuccess=FALSE;
                $res->code=200;
                $res->keyword=$keyw;
                $res->message="조회된 항목이 없습니다.";
                echo json_encode($res,JSON_NUMERIC_CHECK);
                break;
            }
            $res->isSuccess=TRUE;
            $res->code=100;
            $res->keyword=$keyw;
            $res->message="리스트 검색이 완료되었습니다.";
            echo json_encode($res,JSON_NUMERIC_CHECK);
            break;}
        case "specification":{
            $url =$_SERVER['REQUEST_URI'];
            $tmp=explode('/',$url);
            $appid=$tmp[2];
            if(isset($appid)==FALSE){
                $res->result=NULL;
                $res->isSuccess=FALSE;
                $res->code=200;
                $res->message="앱 id가 입력되지 않았습니다.";
                echo json_encode($res,JSON_NUMERIC_CHECK);
                break;
            }
            else if(isValidAppId($appid)){
                $res->result=AppSpecification($appid);
                $res->isSuccess=TRUE;
                $res->code=100;
                $res->message="조회 되었습니다.";
                echo json_encode($res,JSON_NUMERIC_CHECK);
                break;

            }
            $res->result=NULL;
            $res->isSuccess=FALSE;
            $res->code=200;
            $res->message="조회하신 앱은 없는 앱입니다.";
            echo json_encode($res,JSON_NUMERIC_CHECK);
        }
        case "searchword":{
            $word=$_GET['word'];
            $pagenum=$_GET['pagenum'];
            if(isset($word)==FALSE){ //검색어가 입력되지 않았을 때
                $res->result=NULL;
                $res->isSuccess=FALSE;
                $res->code=200;
                $res->message="검색어를 입력해주세요";
                echo json_encode($res,JSON_NUMERIC_CHECK);
                break;
            }

            $res->result=SearchAppList($word,$pagenum);
            if(empty($res->result)){
                $res->result=NULL;
                $res->isSuccess=FALSE;
                $res->code=200;
                $res->message="해당 단어에 검색된 항목이 없습니다.";
                echo json_encode($res,JSON_NUMERIC_CHECK);
                break;
            }
            $res->isSuccess=TRUE;
            $res->code=100;
            $res->message="조회 되었습니다.";
            echo json_encode($res,JSON_NUMERIC_CHECK);
            break;
        }
        case "popularcategorylist":{
            $res->result=PopularCategoryList();
            $res->isSuccess=TRUE;
            $res->code=100;
            $res->message="조회 되었습니다.";
            echo json_encode($res,JSON_NUMERIC_CHECK);
            break;

        }
    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}