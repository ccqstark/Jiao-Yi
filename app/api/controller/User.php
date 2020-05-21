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


class User extends Controller{

    //获取我的关注
    public function myFocus(){

        $user_id = Session::get('user_id');
        $followModel = new model\UserFollow;
        $id_data = $followModel->getFocusId($user_id);       

        if(!$id_data){ 
            return json([
                'resultCode' => 0,
                'msg' => 'no content'
            ]);
        }else{  
             //获取id对应用户名
            $userModel = new model\UserBaseInfo;
            $data = $userModel->getFollowName($id_data);

            return json([
                'resultCode' => 1,
                'data' => $data
            ]);
        }
    }

    //获取我的粉丝
    public function myFans(){

        $user_id = Session::get('user_id');
        $followModel = new model\UserFollow;
        $id_data = $followModel->getFansId($user_id);

        if(!$id_data){ 
            return json([
                'resultCode' => 0,
                'msg' => 'no content'
            ]);
        }else{  
             //获取id对应用户名
            $userModel = new model\UserBaseInfo;
            $data = $userModel->getFollowName($id_data);

            return json([
                'resultCode' => 1,
                'data' => $data
            ]);
        }
    }

    //扩展信息第一页
    public function expandFirstPage(Requset $requset){
        
        $res = $requset->post();
        $dataType = $res['dataType'];

        $expandModel = new model\UserExpand;
        $data = $expandModel->getExpandFirstPage($dataType);

        if(!$data){
            return json([
                'resultCode' => 0,
                'msg' => 'no data'
            ]);
        }else{
            return json([
                'resultCode' => 1,
                'data' => $data,
                'msg'  => 'success'
            ]);
        }
    }

    //扩展信息下一页
    public function expandNextPage(Requset $requset){

        $res = $requset->post();
        $dataType = $res['dataType'];

        $expandModel = new model\UserExpand;
        $data = $expandModel->getExpandNextPage($dataType);

        if(!$data){
            return json([
                'resultCode' => 0,
                'msg' => 'no data'
            ]);
        }else{
            return json([
                'resultCode' => 1,
                'data' => $data,
                'msg'  => 'success'
            ]);
        }
    }

    public function  getIdToChat(Request $request){

        $res = $requset->post();
        $username = $res['username'];

        $userModel = new model\UserBaseInfo;
        $id = $userModel->getIdByUsername($username);
        if($id){
            return json([
                'resultCode'=>1,
                'user_id'=>$id
            ]);
        }else{
            return json([
                'resultCode'=>1,
                'msg'=>'not found'
            ]);
        }
    }
  
    //邮件通知聊天新消息
    public function informChat(){

        $res = $requset->post();
        $toid = $res['toid'];

        $userModel = new model\UserBaseInfo;
        $email = $userModel->getEmailById($toid);
        //生成web聊天地址
        $user_id = Session::get('user_id');
        $chat = 'http://47.107.76.178/jiaoyi/public/index.php/index/chat/chat?fromid='.$toid.'&toid='.$user_id;

        Mailer::chatNoticfication($email,$chat); //发邮箱

    }




}


