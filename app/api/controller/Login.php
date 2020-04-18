<?php
namespace app\api\controller;
use think\Controller;
use think\Request;
use app\api\model;


class Login extends Controller{

    public function login(Request $request){
        $res = $request->post();
        $username = $res['username'];
        $password = $res['password'];
        $userInfo = new model\UserBaseInfo;
        $result = $userInfo->toLogin($username,$password);
        switch ($result)
        {
            case 0:
                return json(['resultCode' => 0,
                                    'msg' => 'user not found']);
                break;
            case -1:
                return json(['resultCode' => -1,
                                    'msg' => 'psw wrong']);
                break;
            case 1:
                return json(['resultCode' => 1,
                                    'msg' => 'success']);
                break;
            default:
                return 0;
        }


    }
}