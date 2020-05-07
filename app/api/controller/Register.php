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


class Register extends Controller{
    //注册验证码
    public function register(Request $request){
        $res = $request->post();
        $username = $res['username'];
        $email = $res['email']; 
        $password = $res['password'];

        //验证格式
        $validate = new validate\UserInfo;
        $vali_result = $validate->check($res);
        if(!$vali_result){ //格式有误
            return json([
                'resultCode' => 0,
                'msg'=>$validate->getError()
            ]);
        }
        
        //用户名已被注册
        $userInfo = new model\UserBaseInfo;
        $exist = $userInfo->findUserExistByName($username);
        if($exist){
            return json([
                'resultCode' => -1,
                'msg' => '此用户名已被注册'
            ]);
        }

        //邮箱已被注册
        $exist = $userInfo->findUserExistByEmail($email);
        if($exist){
            return json([
                'resultCode' => -1,
                'msg' => '此邮箱已被注册'
            ]);
        }

        
        //存入session
        Session::set('username',$username);
        Session::set('email', $email);
        Session::set('password', $password);
       
        $vercode = rand(1000,9999); //随机生成4位数验证码
        Session::set('vercode', $vercode);
        Mailer::mailsender($email,$vercode); //发邮箱

        //格式无误
        return json([
            'resultCode'=>1,
            'msg' => 'success'
        ]);
    }

 

    //判断验证码
    public function judgeVercode(Request $request){

        $res = $request->post();
        $userVercode = $res['vercode'];
        $vercode = Session::get('vercode');
        //验证成功
        if($vercode==$userVercode){
            $username = Session::get('username');
            $email    = Session::get('email');
            $password = Session::get('password');
            //插入数据库
            $userInfo = new model\UserBaseInfo;
            $new_id = $userInfo->toRegister($username,$email,$password); 
            //记录当前登录的id
            Session::set('user_id',$new_id);
            
            return json([
                'resultCode' => 1,
                'msg'  => 'success'  //验证码成功并且插入数据库
            ]);
        }else{
            return json([
                'resultCode' => 0,
                'msg'  => 'failed'   //验证码错误
            ]);
        }
    }






}