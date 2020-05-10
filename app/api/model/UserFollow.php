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
        //去掉占位符0
        array_shift($eachOther);
        
        $len = sizeof($eachOther);
        if($len == 0){  //无互相关注
            return 0;
        }else{
            //转为int
            for($x = 0;$x<$len;$x++){
                $eachOther[$x] = (int)$eachOther[$x];
            } 
            return $eachOther;
        }
        
    }


    //获取我的关注
    public function getFocusId($user_id){

        $db = Db::table('user_follow');
        $thisUser = $db->where(['user_id'=>$user_id])->find();
        $my_focus = $thisUser['focus_list'];
        $my_focus = explode(',', $my_focus); //转为数组

        array_shift($my_focus); //去0
        $len = sizeof($my_focus);

        if($len==0){ //无关注
            return 0; 
        }else{
            //转为int
            for($x = 0;$x<$len;$x++){
                $my_focus[$x] = (int)$my_focus[$x];
            } 
            return $my_focus;
        }   
    }

    //获取我的粉丝
    public function getFansId($user_id){

        $db = Db::table('user_follow');
        $thisUser = $db->where(['user_id'=>$user_id])->find();
        $my_fans = $thisUser['fans_list'];
        $my_focus = explode(',', $my_fans); //转为数组

        array_shift($my_fans); //去0
        $len = sizeof($my_fans);

        if($len==0){ //无粉丝
            return 0; 
        }else{
            //转为int
            for($x = 0;$x<$len;$x++){
                $my_fans[$x] = (int)$my_fans[$x];
            } 
            return $my_fans;
        }  
    }





}