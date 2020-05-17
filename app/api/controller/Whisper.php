<?php
namespace app\api\controller;
use think\Controller;
use think\Request;
use app\api\model;
use app\api\validate;
use app\api\controller\Mailer;
use think\Session;
//CORS跨域
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: token, Origin, X-Requested-With, Content-Type, Accept, Authorization");
header('Access-Control-Allow-Methods: POST,GET,PUT,DELETE');
if(request()->isOptions()){
    exit();
}
//防XSS
// ini_set("session.cookie_httponly", 1);

class Whisper extends Controller{

    //发表悄悄话
    public function post(Request $request){

        $res = $request->post();
        $author_id = Session::get('user_id');
        $whisper_content = $res['content'];
        $token_v = $res['token'];
        //token验证,防CSRF
        $token = model\Token::getToken($author_id);
        if($token_v != $token){
            return json([
                'resultCode' => -100,
                'msg' => 'invalid token'
            ]);
        }

        $WhisperModel = new model\WhisperMainbody;
        $result = $WhisperModel->insertNew($author_id, $whisper_content);

        if($result){
            return json([
                'resultCode' => 1,
                'msg' => 'success'
            ]);
        }else{
            return json([
                'resultCode' => 0,
                'msg' => 'failed'
            ]);
        }
    }

    //浏览悄悄话前5条
    public function browseFirst(){

        $WhisperModel = new model\WhisperMainbody;
        $data = $WhisperModel->getFirstPage();
        
        return json($data);
    }

    //下一页
    public function browseNext(){

        $WhisperModel = new model\WhisperMainbody;
        $data = $WhisperModel->getNextPage();
        
        return json($data);
    }

    //查看悄悄话的评论
    public function viewComment(Request $request){

        $res = $request->post();
        $whisper_id = $res['whisper_id'];

        $WhisperModel = new model\WhisperMainbody;
        $data = $WhisperModel->getComment($whisper_id);

        if($data){
            return json([
                'resultCode' => 1, 
                'data' => $data,
                'msg'  => 'success'
            ]);
        }else{
            return json([
            'resultCode' => 0,
            'msg' => 'no data'
            ]);
        }
    }

    //发表评论
    public function comment(Request $request){

        $res = $request->post();
        $whisper_id = $res['whisper_id'];
        $comment_content = $res['content'];

        $WhisperModel = new model\WhisperMainbody;
        $result = $WhisperModel->addComment($whisper_id, $comment_content);

        if($result){
            return json([
                'resultCode' => 1,
                'msg' => 'success'
            ]);
        }else{
            return json([
                'resultCode' => 0,
                'msg' => 'failed'
            ]);
        }
    }









}
