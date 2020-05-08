<?php
namespace app\api\model;
use think\Model;
use think\Db;
use think\Session;

Db::connect();

class UserFollow extends Model{

    //新增关注
    public function addFollow($user_id,$new_focus){

        //关注着
        $focuser =  Db::table('user_follow')->where(['user_id'=>$user_id])->find();
        $focus_list = $focuser['focus_list'];    
        $focus_list = explode(',', $focus_list); //转为数组
        array_push($focus_list,$new_focus); //向数组添加新元素
        $focus_list = implode(',', $focus_list); //转回字符串
        $result1 = Db::table('user_follow')->where(['user_id'=>$user_id])->update(['focus_list'=>$focus_list]);

        //被关注者
        $by_follower = Db::table('user_follow')->where(['user_id'=>$new_focus])->find();
        $fans_list = $by_follower['fans_list'];
        $fans_list = explode(',', $fans_list); //转为数组
        array_push($fans_list,$user_id); //向数组添加新元素
        $fans_list = implode(',', $fans_list); //转回字符串
        $result2 = Db::table('user_follow')->where(['user_id'=>$new_focus])->update(['fans_list'=>$fans_list]);

        $result = $result1+$result2;
        return $result;
    }


    //获得互相关注的用户id，以数组返回
    public static function getFollowEachOther($user_id){

        $db = Db::table('user_follow');
        $thisUser = $db->where(['user_id'=>$user_id])->find();
        $my_focus = $thisUser['focus_list'];
        $my_focus = explode(',', $my_focus);
        $my_fans = $thisUser['fans_list'];
        $my_fans = explode(',', $my_fans);  

        //转为数组求交集
        $eachOther = array_intersect($my_focus,$my_fans);
        //删除0
        array_shift($eachOther);
        //转为int
        $len = sizeof($eachOther);
        for($x = 0;$x<$len;$x++){
            $eachOther[$x] = (int)$eachOther[$x];
        } 
        
        return $eachOther;
    }


}