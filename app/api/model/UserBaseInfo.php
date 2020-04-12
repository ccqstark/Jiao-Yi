<?php
namespace app\api\model;
use think\Model;
use think\Db;

class UserBaseInfo extends Model{

    public function toLogin($username,$password){
        $db = Db::table('user_base_info');
        $thisUser = $db->where(['username'=>$username])->find();
        if(!$thisUser){
            return 0;
        }else{
            if($password!=$thisUser['password']){
                return -1;
            }else{
                return 1;
            }
        }

    }



}
