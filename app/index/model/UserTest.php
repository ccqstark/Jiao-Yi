<?php
namespace app\index\model;
use think\Model;
use think\Db;

class UserTest extends Model{

    public function insertUserInfo($userInfo){
    //    $res = Db::connect();
        $db = Db::table('user_test');
        $result = $db->insert($userInfo);
        return $result;
    }

    public function retrieve(){
        $db = Db::table('user_test');
        $result = $db->select();
        return $result;

    }

    public function deteteUser($deleteID){
        $db = Db::table('user_test');
        $result = $db->delete($deleteID);
        return $result;
    }

    public function changeInfo($usename,$field,$newMsg){
        $db = Db::table('user_test');
        $result = $db->where(['username'=>$usename])->update([$field=>$newMsg]);
        return $result;

    }

}