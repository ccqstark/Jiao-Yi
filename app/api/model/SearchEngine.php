<?php
namespace app\api\model;
use think\Model;
use think\Db;
use app\api\model\VicWord;

Db::connect();

class SearchEngine extends Model{

    //分词（VicWord）
    public static function cutWord($phrase){

        $word_array = model\VicWord\Cut::goCut($phrase);

        return $word_array;
    }

    //进行搜索（模糊查询）
    public function goSearch($keyWord){

        $keyWord_array = self::cutWord($keyWord);

        $search_result = array();

        //找人
        foreach ($keyWord_array as $words){
            $word = $words[0];
            $record_array= Db::table('user_base_info')
                            ->where('user_name','like','%'.$word.'%')
                            ->select();
            foreach($record_array as $record){
                array_push($search_result,$record);
            }
        }

        //找帖子
        foreach ($keyWord_array as $words){
            $word = $words[0];
            $record_array= Db::table('commodity_base_info')
                            ->where('commodity_title','like','%'.$word.'%')
                            ->select();
            foreach($record_array as $record){
                array_push($search_result,$record);
            }
        }

        return $search_result;
    }




}