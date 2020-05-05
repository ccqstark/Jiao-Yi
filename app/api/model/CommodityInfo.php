<?php
namespace app\api\model;
use think\Model;
use think\Db;

class CommodityInfo extends Model{
    //插入商品帖子信息
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

        //更新索引

        $result = $result1+$result2;
        return $result;

    }




}