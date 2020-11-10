<?php
require 'function.php';

const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";

$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        case"appreview":{
            $url =$_SERVER['REQUEST_URI'];
            $tmp=explode('/',$url);
            $appid=$tmp[2];
            $order=$_GET['order'];

            if(isset($order)==FALSE){$order='helpful';} //default

            if(isValidAppId($appid)==FALSE){
                $res->result=NULL;
                $res->isSuccess=FALSE;
                $res->code=200;
                $res->message="존재하지 않는 어플입니다.";
                echo json_encode($res,JSON_NUMERIC_CHECK);
                break;
            }
            $res->result=SearchReview($appid,$order);
            $res->isSuccess=TRUE;
            $res->code=100;
            $res->message="조회가 완료되었습니다.";
            echo json_encode($res,JSON_NUMERIC_CHECK);
            break;
        }
        case "insertreview":{
            $url =$_SERVER['REQUEST_URI'];
            $tmp=explode('/',$url);
            $appid=$tmp[2];
            $jwt=$_SERVER["HTTP_JWT"];
            if(isValidAppId($appid)==FALSE){
                $res->isSuccess=FALSE;
                $res->code=200;
                $res->message="존재하지 않는 어플입니다.";
                echo json_encode($res,JSON_NUMERIC_CHECK);
                break;
            }
            if(isset($req->Stars)){$stars=$req->Stars;}
            else{$stars=4;} //default
            if(isValidHeader($jwt,JWT_SECRET_KEY)){ //올바른 유저의 접근임을 확인

                if (isset($req->Title) AND isset($req->Comment)==FALSE){ // 댓글 내용이 없는 경우
                        $res->isSuccess=FALSE;
                        $res->code=200;
                        $res->message="댓글 내용을 입력해주세요";
                        echo json_encode($res,JSON_NUMERIC_CHECK);
                        return;
                }
                elseif(isset($req->Comment) AND isset($req->Title)==FALSE) { //제목을 입력하지 않은 경우
                        $res->isSuccess=FALSE;
                        $res->code=200;
                        $res->message="제목을 입력해주세요";
                        echo json_encode($res,JSON_NUMERIC_CHECK);
                        return;
                }
                elseif(isset($req->Title)==FALSE AND isset($req->Comment)==FALSE){ //그냥 평가
                    $data=getDataByJWToken($jwt, JWT_SECRET_KEY);
                    InsertReview($data,$appid,NULL,NULL,$stars);
                    $res->isSuccess=TRUE;
                    $res->code=100;
                    $res->message="평가가 완료되었습니다.";
                    echo json_encode($res,JSON_NUMERIC_CHECK);
                    return;

                }
                //제대로된 댓글 입력.
                $data=getDataByJWToken($jwt, JWT_SECRET_KEY);
                InsertReview($data,$appid,$req->Title,$req->Comment,$stars);
                $res->isSuccess=TRUE;
                $res->code=100;
                $res->message="댓글이 입력되었습니다.";
                echo json_encode($res,JSON_NUMERIC_CHECK);
                return;
                }
            }
            $res->isSuccess=FALSE;
            $res->code=200;
            $res->message="잘못된 접근입니다. 다시 로그인부터 진행해주세요";
            echo json_encode($res,JSON_NUMERIC_CHECK);
            return;
        case "insertanswer":{
            $url =$_SERVER['REQUEST_URI'];
            $tmp=explode('/',$url);
            $reviewid=$tmp[2];
            $jwt=$_SERVER["HTTP_JWT"];

            if(isValidHeader($jwt,JWT_SECRET_KEY)){ //올바른 유저의 접근임을 확인
                $data=getDataByJWToken($jwt, JWT_SECRET_KEY);
                if(isValidDeveloper($data->nickname,$reviewid)){
                    //로그인한 아이디가 그 어플의 개발자인지 확인
                    InsertAnswer($reviewid,$req->Answer);
                    $res->isSuccess=TRUE;
                    $res->code=100;
                    $res->message="답변이 입력되었습니다.";
                    echo json_encode($res,JSON_NUMERIC_CHECK);
                    return;
                }
                $res->isSuccess=FALSE;
                $res->code=200;
                $res->message="접근하실 수 없는 항목입니다(개발자만 가능)";
                echo json_encode($res,JSON_NUMERIC_CHECK);
                return;
            }
            $res->isSuccess=FALSE;
            $res->code=200;
            $res->message="잘못된 접근입니다. 다시 로그인부터 진행해주세요";
            echo json_encode($res,JSON_NUMERIC_CHECK);
            return;
            }
        case "delreview":{
            $url =$_SERVER['REQUEST_URI'];
            $tmp=explode('/',$url);
            $reviewid=$tmp[2];
            $jwt=$_SERVER["HTTP_JWT"];
            if(isValidHeader($jwt,JWT_SECRET_KEY)){
                $data=getDataByJWToken($jwt, JWT_SECRET_KEY);
                if(isValidUserReview($data->id,$reviewid)){
                    delreview($reviewid);
                    $res->isSuccess=TRUE;
                    $res->code=100;
                    $res->message="댓글이 삭제되었습니다.";
                    echo json_encode($res,JSON_NUMERIC_CHECK);
                    return;
                    }
                $res->isSuccess=FALSE;
                $res->code=200;
                $res->message="권한이 없습니다";
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