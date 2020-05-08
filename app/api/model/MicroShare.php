<?php
namespace app\api\model;
use think\Model;
use think\Db;
use think\Session;
define('PERPAGE',2); //每页帖子数

Db::connect();

class MicroShare extends Model{

    //发布新微享
    public function postNew($author_id, $content){

        $db = Db::table('micro_share');
        $result = $db->indert([
            'share_author_id' => $author_id,
            'share_content'   => $content
        ]);

        return $result;
    }


    //获得权限内可见内容
    public static function getVisibleShare($user_id){
         
        //获取互相关注的人
        $eachOther = UserFollow::getFollowEachOther($user_id);
        $visible_share = array();
        //互相关注的人发布的内容
        foreach($eachOther as $eachOther_id){
            $content_array = Db::table('micro_share')
                            ->where(['share_author_id'=>$eachOther_id])
                            ->select(); 

            foreach($content_array as $content_element){
                    array_push($visible_share,$content_element);
            }                                                                         
        }

        return $visible_share;
    }


    //分页
    public function getFirstPage(){

       


    }






}


