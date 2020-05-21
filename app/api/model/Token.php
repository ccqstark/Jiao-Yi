<?php
namespace app\api\model;
use think\Model;
use think\Session;
use think\cache\driver\Redis;

class Token {

    public static function generateToken($useridentity,$password){

        $user_id = Session::get('user_id');
        $str = $user_id.$useridentity.$password;
        $token = md5($str);
        //redis缓存
        $redis = new Redis();
        $redis->set('token'.$user_id, $token);
        // $redis -> setTimeout('token'.$user_id,300);

        return $token;
    }


    public static function getToken($user_id){

        $redis = new Redis();
        $token = $redis->get('token'.$user_id);

        return $token;
    }

    




}