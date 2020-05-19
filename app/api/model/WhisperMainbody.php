<?php
namespace app\api\model;
use think\Model;
use think\Db;
use think\Session;
use think\cache\driver\Redis;
define('PERPAGE',2); //每页帖子数

Db::connect();

class WhisperMainbody extends Model{

    public function insertNew($author_id, $content){

        $db = Db::table('whisper_mainbody');
        $result1 = $db->insertGetId([
            'whisper_author_id' => $author_id,
            'whisper_content'   => $content,
            'like' => 0
        ]);

        //FIXME:新发布更新expand表
        $userExpand =  Db::table('user_expand')->where(['user_id'=>$author_id])->find();
        $whisper_list = $userExpand['my_whisper'];    
        $whisper_list = explode(',', $whisper_list); //转为数组
        array_push($whisper_list,$result1); //向数组添加新元素
        $whisper_list = implode(',', $whisper_list); //转回字符串
        $result2 = Db::table('user_expand')
                    ->where(['user_id'=>$author_id])
                    ->update(['my_whisper'=>$whisper_list]);
        
        return $result1+$result2;
    }


    //获取悄悄话第一页
    public function getFirstPage(){

        $db = Db::table('whisper_mainbody');
        //分页，倒序
        $data = $db->page(1,PERPAGE)->order('whisper_id desc')->select();
        Session::set('whisper_page',1);

        return $data;
    }

     //获取下一页悄悄话
     public function getNextPage(){

        $db = Db::table('whisper_mainbody');
        $page = Session::get('whisper_page') + 1;
        //分页，倒序
        $data = $db->page($page,PERPAGE)->order('whisper_id desc')->select();
        Session::set('whisper_page',$page);

        return $data;
    }


    //获取评论
    public function getComment($whisper_id){

        $db = Db::table('whisper_comment');
        $data = $db->where(['whisper_id' => $whisper_id])->select();

        return $data;
    }

    //新增评论
    public function addComment($whisper_id, $content){

        $db = Db::table('whisper_comment');
        $result = $db->insert([
                'whisper_id'=>$whisper_id,
                'comment_content'=>$content
            ]);

        return $result;
    }

    //获取我的悄悄话
    public static function getMyWhisper($id_data){

        $whisper_array = array();
        foreach($id_data as $id){
            $thisWhisper = Db::table('whisper_mainbody')->where(['whisper_id'=>$id])->find();
            array_push($whisper_array,$thisWhisper);
        }
    
        //倒序排序
        array_multisort(array_column($whisper_array,'whisper_id') ,SORT_DESC, $whisper_array);

        //redis缓存
        $redis = new Redis();
        $redis->set('my_whisper'.$user_id, $whisper_array);
    
        return $whisper_array;
    }


}