<?php
namespace app\api\model;
use think\Model;
use think\Db;
use think\Session;
use think\cache\driver\Redis;
define('PERPAGE',2); //每页帖子数

Db::connect();

class MicroShare extends Model{

    //发布新微享
    public function postNew($author_id, $content){

        $db = Db::table('micro_share');
        $result1 = $db->indertGetId([
            'share_author_id' => $author_id,
            'share_content'   => $content
        ]);

        //FIXME:新发布更新expand表
        $userExpand =  Db::table('user_expand')->where(['user_id'=>$author_id])->find();
        $share_list = $userExpand['my_share'];    
        $share_list = explode(',', $share_list); //转为数组
        array_push($share_list,$result1); //向数组添加新元素
        $share_list = implode(',', $share_list); //转回字符串
        $result2 = Db::table('user_expand')
                    ->where(['user_id'=>$author_id])
                    ->update(['my_share'=>$share_list]);

        return $result1+$result2;
    }


    //获得微享权限内可见内容（权限分级）
    public static function getVisibleShare($user_id){
         
        //获取互相关注的人
        $eachOther = UserFollow::getFollowEachOther($user_id);

        if($eachOther == 0){ //无互相关注的人
            return 0;
        }else{  //有互相关注的人
            $visible_share = array();
            //互相关注的人发布的内容
            $content_flag = 0; //是否有发布内容的标志
            foreach($eachOther as $eachOther_id){
                $content_array = Db::table('micro_share')
                                ->where(['share_author_id'=>$eachOther_id])
                                ->select(); 

                if ($content_array != 0){
                    $content_flag = 1;
                    foreach($content_array as $content_element){
                            array_push($visible_share,$content_element);
                    }    
                }                                                                     
            }

            if(!$content_flag){  //无内容
                return 0;
            }else{   //有内容       
                //倒序排序
                array_multisort(array_column($visible_share,'share_id') ,SORT_DESC, $visible_share);

                //使用redis缓存，高并发方案
                $redis = new Redis();
                $redis->set('share'.$user_id, $visible_share);
            
                return $visible_share;
            }
        }
    }


    //微享第一页
    public function getFirstPage(){

        $user_id = Session::get('user_id');
        //获得排好序的全部内容
        $share = self::getVisibleShare($user_id);

        if(!$share){  //无内容
            return 0;
        }
        else{  //有内容
            $total = count($share);

            //总内容只有一页
            if($total<=PERPAGE){
                Session::set('sharePage'.$user_id,0);
                return $share;
            }
            //内容大于一页时
            else{               
                $page_data = array();
                for($i = 0;$i<PERPAGE;$i++){
                    array_push($page_data,$share[$i]);
                }
                //记录下一页开始的记录的下标
                Session::set('sharePage'.$user_id,PERPAGE);

                return $page_data;
            }
        }
    }

    //微享下一页
    public function getNextPage(){
    
        $now_page = Session::get('sharePage'.$user_id);
        if($now_page==0){ //没有内容了
            return 0;
        }
        else{
            $user_id = Session::get('user_id');
            //从redis缓存中获取
            $redis = new Redis();
            $share = $redis->get('share'.$user_id);
            $page_data = array();
             
            $total = count($share);

            //只剩下一页
            if($now_page+PERPAGE>$total-1){
                $rest = $total-$now_page;
                for($i = 0;$i<rest;$i++){
                    array_push($page_data,$share[$now_page+$i]);
                }
                Session::set('sharePage'.$user_id,0); //置0
                
                return $page_data;
            }
            //剩下大于一页  
            else{                    
                for($i = 0;$i<PERPAGE;$i++){
                    array_push($page_data,$share[$now_page+$i]);
                }
                Session::set('sharePage'.$user_id, $now_page+PERPAGE);
                
                return $page_data;
            }
        }
    }

    //获取我发布的微享
    public static function getMyShare($id_data){

        $share_array = array();
        foreach($id_data as $id){
            $thisShare = Db::table('micro_share')->where(['share_id'=>$id])->find();
            array_push($share_array,$thisShare);
        }
    
        //倒序排序
        array_multisort(array_column($share_array,'whisper_id') ,SORT_DESC, $share_array);

        //redis缓存
        $redis = new Redis();
        $redis->set('my_share'.$user_id, $share_array);
    
        return $share_array;
    }


     //获取评论
     public function getComment($share_id){

        $db = Db::table('share_comment');
        $data = $db->where(['share_id' => $share_id])->select();

        return $data;
    }

    //新增评论
    public function addComment($share_id, $content){

        $user_id = Session::get($user_id);
        $thisUser = Db::table('user_base_info')->where(['user_id'=>$user_id])->find();
        $user_name = $thisUser['user_name'];

        $result = Db::table('share_comment')
                    ->insert([
                    'share_id'=>$share_id,
                    'comment_user_name'=>$user_name,
                    'comment_content'=>$content
                ]);

        return $result;
    }





}


