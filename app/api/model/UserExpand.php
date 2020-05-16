<?php
namespace app\api\model;
use think\Model;
use think\Db;
use think\Session;
use think\cache\driver\Redis;
define('PERPAGE',2); //每页帖子数

Db::connect();

class UserExpand extends Model{

    //获取我的收藏、我的发布的id数组
    public static function getMyExpandId($user_id, $dataType){

        $db = Db::table('user_expand');
        $thisUser = $db->where(['user_id'=>$user_id])->find();
        //根据参数来确定获取类型
        switch($dataType)
        {
            case 0:
                $my_data = $thisUser['my_favorite'];
                break;
            case 1:
                $my_data = $thisUser['my_commodity'];
                break;
            case 2:
                $my_data = $thisUser['my_whisper'];
                break;
            case 3:
                $my_data = $thisUser['my_share'];
                break;
        }
    
        $my_data = explode(',', $my_data); //得到数组
        array_shift($my_data); //去0
        $len = sizeof($my_data);

        if($len==0){ 
            return 0; 
        }else{
            //转为int
            for($x = 0;$x<$len;$x++){
                $my_data[$x] = (int)$my_data[$x];
            } 
            return $my_data;
        }  
    }


    //获取我的扩展信息第一页时的数据准备
    public static function getMyExpandData($user_id, $dataType){

        //获取id数组
        $id_data = self::getMyExpandId($user_id,$dataType);
        if($id_data==0){
            return 0;
        }

        //根据类型获取数据
        switch($dataType){
            case 0:
                $data_array = model\CommodityBaseInfo::getMyCommodity($id_data,$dataType);
                break;
            case 1:
                $data_array = model\CommodityBaseInfo::getMyCommodity($id_data,$dataType);
                break;
            case 2:
                $data_array = model\WhisperMainbody::getMyWhisper($id_data);
                break;
            case 3:
                $data_array = model\MicroShare::getMyShare($id_data);
                break;
        }

        return $data_array;
    }


    //获取扩展信息第一页
    public function getExpandFirstPage($dataType){

        $user_id = Session::get('user_id');
        //获得排好序的全部内容
        $data_array = self::getMyExpandData($user_id,$dataType);

        if(!$data_array){  //无内容
            return 0;
        }
        else{              
            //有内容
            $total = count($data_array);

            //总内容只有一页
            if($total<=PERPAGE){
                switch($dataType){
                    case 0:
                        Session::set('my_favo_page'.$user_id ,0);
                        break;
                    case 1:
                        Session::set('my_commod_page'.$user_id ,0);
                        break;
                    case 2:
                        Session::set('my_whisper_page'.$user_id ,0);
                        break;
                    case 3:
                        Session::set('my_share_page'.$user_id ,0);
                        break;
                }

                return $share;
            }
            //内容大于一页时
            else{               
                $page_data = array();
                for($i = 0;$i<PERPAGE;$i++){
                    array_push($page_data,$share[$i]);
                }
                //记录下一页开始的记录的下标
                switch($dataType){
                    case 0:
                        Session::set('my_favo_page'.$user_id ,PERPAGE);
                        break;
                    case 1:
                        Session::set('my_commod_page'.$user_id ,PERPAGE);
                        break;
                    case 2:
                        Session::set('my_whisper_page'.$user_id ,PERPAGE);
                        break;
                    case 3:
                        Session::set('my_share_page'.$user_id ,PERPAGE);
                        break;
                }
                
                return $page_data;
            } 
        }
    }

    //获取扩展信息下一页
    public function getExpandNextPage($dataType){
        
        //从Session中获取页数
        switch($dataType){
            case 0:
                $now_page = Session::get('my_favo_page'.$user_id);
                break;
            case 1:
                $now_page = Session::get('my_commod_page'.$user_id);
                break;
            case 2:
                $now_page = Session::get('my_whisper_page'.$user_id);
                break;
            case 3:
                $now_page = Session::get('my_share_page'.$user_id);
                break;
        }

        if($now_page==0){ //没有内容了
            return 0;
        }
        else{
            $user_id = Session::get('user_id');
            //从redis缓存中获取
            $redis = new Redis();
            switch($dataType){
                case 0:
                    $data_array = $redis->get('my_favo_page'.$user_id);
                    break;
                case 1:
                    $data_array = $redis->get('my_commod_page'.$user_id);
                    break;
                case 2:
                    $data_array = $redis->get('my_whisper_page'.$user_id);
                    break;
                case 3:
                    $data_array = $redis->get('my_share_page'.$user_id);
                    break;    
            }

            $page_data = array();
            $total = count($data_array);
            //只剩下一页
            if($now_page+PERPAGE>$total-1){
                $rest = $total-$now_page;
                for($i = 0;$i<rest;$i++){
                    array_push($page_data,$data_array[$now_page+$i]);
                }

                switch($dataType){
                    case 0:
                        Session::set('my_favo_page'.$user_id ,0);
                        break;
                    case 1:
                        Session::set('my_commod_page'.$user_id ,0);
                        break;
                    case 2:
                        Session::set('my_whisper_page'.$user_id ,0);
                        break;
                    case 3:
                        Session::set('my_share_page'.$user_id ,0);
                        break;
                }
    
                return $page_data;
            }
            //剩下大于一页  
            else{                    
                for($i = 0;$i<PERPAGE;$i++){
                    array_push($page_data,$share[$now_page+$i]);
                }

                switch($dataType){
                    case 0:
                        Session::set('my_favo_page'.$user_id ,$now_page+PERPAGE);
                        break;
                    case 1:
                        Session::set('my_commod_page'.$user_id ,$now_page+PERPAGE);
                        break;
                    case 2:
                        Session::set('my_whisper_page'.$user_id ,$now_page+PERPAGE);
                        break;
                    case 3:
                        Session::set('my_share_page'.$user_id ,$now_page+PERPAGE);
                        break;
                }
                
                return $page_data;
            }
        }
    }



    

    



}
