<?php
namespace app\api\model;
use think\Model;
use think\Db;
use think\Session;
define('PERPAGE',2); //每页帖子数

Db::connect();

class WhisperMainbody extends Model{

    public function insertNew($author_id, $content){

        $db = Db::table('whisper_mainbody');
        $result = $db->insert([
            'whisper_author_id' => $author_id,
            'whisper_content'   => $content,
            'like' => 0
        ]);
        
        return $result;
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


}