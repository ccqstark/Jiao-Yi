<?php
namespace app\api\model;
use think\Model;
use think\Db;
use think\Session;
define('PERPAGE',2); //每页帖子数

Db::connect();
class CommodityBaseInfo extends Model{
    
    //插入新发布的商品帖子
    public function commodityInsert($title,$description,$price,$location,$contact){
        $db1 = Db::table('commodity_base_info');
        $db2 = Db::table('commodity_detail');

        $result1 = $db1->insert([
                'commodity_title'    => $title,
                'commodity_price'    => $price,
                'commodity_location' => $location
            ]);
        $result2 = $db2->insert([
            'commodity_description' => $description,
            'contact'               => $contact,
            'like'                  => 0
        ]);


        $result = $result1+$result2;
        return $result;

    }

    //获取第一页，在首页第一次加载时调用
    public function getFirstPage(){

        $db = Db::table('commodity_base_info');
        //分页，倒序
        $data = $db->page(1,PERPAGE)->order('commodity_id desc')->select();
        Session::set('page',1);
        return $data;

    }

    //获取下一页，每次调用返回5条数据
    public function getNextPage(){

        $db = Db::table('commodity_base_info');
        $page = Session::get('page') + 1;
        //分页，倒序
        $data = $db->page($page,PERPAGE)->order('commodity_id desc')->select();
        Session::set('page',$page);

        return $data;
    }

    public function getDetail($commodity_id){

        $db = Db::table('commodity_base_info');
        //JOIN优化子查询
        $target = $db->alias('b')
                     ->join('commodity_detail d','b.commodity_id = d.commodity_id')
                     ->where(['b.commodity_id' => $commodity_id])
                     ->find();
        
        return $target;
    }



}