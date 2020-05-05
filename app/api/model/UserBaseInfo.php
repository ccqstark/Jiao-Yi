<?php
namespace app\api\model;
use think\Model;
use think\Db;

class UserBaseInfo extends Model{
    //登录
    public function LoginByUsername($username,$password){
        $db = Db::table('user_base_info');
        $thisUser = $db->where(['user_name'=>$username])->find();  //找用户记录
        $password = md5($password);

        if(!$thisUser){  //用户不存在
            return 0;
        }else{
            if($password!=$thisUser['user_password']){  //密码判断
                return -1;
            }else{
                return 1;
            }
        }

    }


    public function LoginByEmail($email,$password){
        $db = Db::table('user_base_info');
        $thisUser = $db->where(['email'=>$email])->find();  //找用户记录
        $password = md5($password);

        if(!$thisUser){  //用户不存在
            return 0;
        }else{
            if($password!=$thisUser['user_password']){  //密码判断
                return -1;
            }else{
                return 1;
            }
        }

    }


    //注册
    public function toRegister($username,$email,$password){
        $db = Db::table('user_base_info');
        $password = md5($password); //md5加密

        $result = $db->insert([
                    'user_name' => $username,
                    'email' => $email,
                    'user_password' => $password,
                ]);
        
        return $result;  //成功插入记录数1

    }


    //找到邮箱（修改密码用的）
    public function findEmail($username){

        $db = Db::table('user_base_info');
        $thisUser = $db->where(['user_name'=>$username])->find();
        
        if(!$thisUser){  //用户不存在
            return 0;
        }else{
            $email = $thisUser['email'];
            return $email;
        }

    }


    public function updatePassword($username,$new_password){
        
        $db = Db::table('user_base_info');
        $new_password = md5($new_password); //加密
        $result = $db->where(['user_name'=>$username])
                    ->update(['user_password'=>$new_password]);
        return $result;

    }

    public function findUserExist($username){
        $db = Db::table('user_base_info');
        $thisUser = $db->where(['user_name'=>$username])->find();

        if(!$thisUser){
            return 0;   //不存在
        }else{
            return 1;  //存在
        }
        
    }




}
