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


class Login extends Controller{

    public function login(Request $request){

        $res = $request->post();
        $useridentity = $res['useridentity'];
        $password = $res['password'];

        $userdata = ['useridentity'=>$useridentity];
        //判断是用户名还是邮箱
        $validate = new validate\LoginByWhat;
        $vali_result = $validate->check($userdata);

        $userInfo = new model\UserBaseInfo;
        if($vali_result){  //邮箱登录
            $result = $userInfo->LoginByEmail($useridentity,$password);
            //记录当前登录的user_id
            if($result==1){
                $user_id = $userInfo->getIdByEmail($useridentity);
                Session::set('user_id',$user_id);
            }

        }else{ //用户名登录  
            $result = $userInfo->LoginByUsername($useridentity,$password);
            //记录当前登录的user_id
            if($result==1){
                $user_id = $userInfo->getIdByUsername($useridentity);
                Session::set('user_id',$user_id);
            }
        }

        
        switch ($result)
        {
            case 0:
                //用户不存在
                return json(['resultCode' => 0,
                                    'msg' => 'user not found']);
                break;
            case -1:
                //密码错误
                return json(['resultCode' => -1,
                                    'msg' => 'psw wrong']);
                break;
            case 1:
                //登录成功
                return json(['resultCode' => 1,
                                    'msg' => 'success']);
                break;
            default:
                return 0;
        }


    }

    //修改密码验证码
    public function changepwd(Request $request){

        $res = $request->post();
        $username = $res['username'];

        $userInfo = new model\UserBaseInfo;
        $email = $userInfo->findEmail($username);

        if(!$email){
            return json([
                'resultCode' => 0,
                'msg' => 'user not found'
            ]);
        }else{
            Session::set('username',$username);

            $vercode = rand(1000,9999); //随机生成4位数验证码
            Session::set('vercode', $vercode);
            Mailer::mailsender($email,$vercode); //发邮箱
            
            return json([
                'resultCode' => 1,
                'msg' =>'success'
            ]);

        }

    }

    //判断验证码
    public function judgeVercode(Request $request){

        $res = $request->post();
        $userVercode = $res['vercode'];
        $vercode = Session::get('vercode');
        if($userVercode==$vercode){
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


    //修改密码完成
    public function changeDone(Request $request){
        $res = $request->post();
        $new_password = $res['new_password'];
        $username = Session::get('username');

        $userInfo = new model\UserBaseInfo;
        $result = $userInfo->updatePassword($username,$new_password);

        if(!$result){
            return json([
                'resultCode' => 0,
                'msg' => 'failed'
            ]);
        }else{
            return json([
                'resultCode'=> 1,
                'msg' => 'success'
            ]);
        }
    }






}