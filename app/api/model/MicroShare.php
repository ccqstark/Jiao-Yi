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
        $result = $db->indert([
            'share_author_id' => $author_id,
            'share_content'   => $content
        ]);

        return $result;
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
                Session::set('sharePage',0);
                return $share;
            }
            //内容大于一页时
            else{               
                $page_data = array();
                for($i = 0;$i<PERPAGE;$i++){
                    array_push($page_data,$share[$i]);
                }
                //记录下一页开始的记录的下标
                Session::set('sharePage',PERPAGE);

                return $page_data;
            }
        }
    }

    //微享下一页
    public function getNextPage(){
    
        $now_page = Session::get('sharePage');
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
                Session::set('sharePage',0); //置0
                
                return $page_data;
            }
            //剩下大于一页  
            else{                    
                for($i = 0;$i<PERPAGE;$i++){
                    array_push($page_data,$share[$now_page+$i]);
                }
                Session::set('sharePage',$now_page+PERPAGE);
                
                return $page_data;
            }
        }
    }









}


