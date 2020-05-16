<?php
namespace app\api\model;
use think\Model;
use think\Db;

Db::connect();
class UserBaseInfo extends Model{
    
    //通过登录用户名登录
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

    //通过邮箱登录
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

    
    //通过邮箱获取ID
    public function getIdByEmail($email){

        $db = Db::table('user_base_info');
        $thisUser = $db->where(['email'=>$email])->find();

        if(!$thisUser){
            return 0;   //不存在
        }else{
            return $thisUser['user_id']; //存在
        }
    }

    //通过用户名获取ID
    public function getIdByUsername($username){

        $db = Db::table('user_base_info');
        $thisUser = $db->where(['user_name' => $username])->find();

        if(!$thisUser){
            return 0;   //不存在
        }else{
            return $thisUser['user_id']; //存在
        }
    }


    //通过用户名判断用户是否已存在，存在则不能注册
    public function findUserExistByName($username){
        $db = Db::table('user_base_info');
        $thisUser = $db->where(['user_name'=>$username])->find();

        if(!$thisUser){
            return 0;   //不存在
        }else{
            return 1;  //存在
        }
        
    }


    //通过邮箱判断用户是否已存在，存在则不能注册
    public function findUserExistByEmail($email){
        $db = Db::table('user_base_info');
        $thisUser = $db->where(['email'=>$email])->find();

        if(!$thisUser){
            return 0;   //不存在
        }else{
            return 1;  //存在
        }      
    }


    //注册成功，插入数据库
    public function toRegister($username,$email,$password){
        $db = Db::table('user_base_info');
        $password = md5($password); //md5加密

        $result_id = $db->insertGetId([
                    'user_name' => $username,
                    'email' => $email,
                    'user_password' => $password,
                ]);
        
        return $result_id;  //成功插入记录数1

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

    //更新数据库中的密码
    public function updatePassword($username,$new_password){
        
        $db = Db::table('user_base_info');
        $new_password = md5($new_password); //加密
        $result = $db->where(['user_name'=>$username])
                    ->update(['user_password'=>$new_password]);
        return $result;

    }

    //用id获取用户名
    public function getFollowName($id_data){

        $name_array = array();
        foreach($id_data as $id){
            $thisUser = Db::table('user_base_info')->where(['user_id'=>$id])->find();
            $nameOfUser = $thisUser['user_name'];
            array_push($name_array,$nameOfUser);
        }

        return $name_array;
    }



}
