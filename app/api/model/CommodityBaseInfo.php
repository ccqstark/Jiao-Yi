<?php
namespace app\api\model;
use think\Model;
use think\Db;
use think\Session;
use think\cache\driver\Redis;
define('PERPAGE',2); //每页帖子数

Db::connect();
class CommodityBaseInfo extends Model{
    
    //插入新发布的商品帖子
    public function commodityInsert($author_id, $title, $description, $price, $location, $contact){
        $db1 = Db::table('commodity_base_info');
        $db2 = Db::table('commodity_detail');

        $result1 = $db1->insertGetId([
                'commodity_author_id'=> $author_id,
                'commodity_title'    => $title,
                'commodity_price'    => $price,
                'commodity_location' => $location
            ]);
        $result2 = $db2->insert([
            'commodity_description' => $description,
            'contact'               => $contact,
            'like'                  => 0
        ]);
        //FIXME:新发布更新expand表
        $userExpand =  Db::table('user_expand')->where(['user_id'=>$author_id])->find();
        $commod_list = $userExpand['my_commodity'];    
        $commod_list = explode(',', $commod_list); //转为数组
        array_push($commod_list,$result1); //向数组添加新元素
        $commod_list = implode(',', $commod_list); //转回字符串
        $result3 = Db::table('user_expand')
                    ->where(['user_id'=>$author_id])
                    ->update(['my_commodity'=>$commod_list]);

        $result = $result1+$result2+$result3;
        return $result;
    }


    //获取第一页，在首页第一次加载时调用
    public function getFirstPage(){

        $db = Db::table('commodity_base_info');
        //分页，倒序
        $data = $db->page(1,PERPAGE)->order('commodity_id desc')->select();
        Session::set('commodity_page',1);
        
        return $data;
    }

    //获取下一页，每次调用返回5条数据
    public function getNextPage(){

        $db = Db::table('commodity_base_info');
        $page = Session::get('commodity_page') + 1;
        //分页，倒序
        $data = $db->page($page,PERPAGE)->order('commodity_id desc')->select();
        Session::set('commodity_page',$page);

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


    //获取收藏商品、我发布的商品
    public static function getMyCommodity($id_data,$commodityType){

        $commodity_array = array();
        foreach($id_data as $id){
            $thisCommodity = Db::table('commodity_base_info')->where(['commodity_id'=>$id])->find();
            array_push($commodity_array,$thisCommodity);
        }
    
        //倒序排序
        array_multisort(array_column($commodity_array,'commodity_id') ,SORT_DESC, $commodity_array);

        //redis缓存
        $redis = new Redis();
        switch($commodityType)
        {
            case 0:
                $redis->set('my_favo'.$user_id, $commodity_array);
                break;
            case 1:
                $redis->set('my_commodity'.$user_id, $commodity_array);
                break;
        }
        
        return $commodity_array;
    }


    //收藏此帖子
    public function addFavo($new_favo){ 

        $user_id = Session::get('user_id');
        $userExpand =  Db::table('user_expand')->where(['user_id'=>$user_id])->find();
        $favo_list = $userExpand['my_favorite'];    
        $favo_list = explode(',', $favo_list); //转为数组
        array_push($favo_list,$new_favo); //向数组添加新元素
        $favo_list = implode(',', $favo_list); //转回字符串
        //添加到我的收藏
        $result1 = Db::table('user_expand')
                    ->where(['user_id'=>$user_id])
                    ->update(['my_favorite'=>$favo_list]);
        //商品收藏数+1
        $result2 = Db::table('commodity_detail')
                    ->where(['commodity_id'=>$new_favo])
                    ->setInc('like',1);
       
        return $result1+$result2;
    }


    

}