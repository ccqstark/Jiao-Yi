<?php
namespace app\api\controller;
use think\Controller;
use think\Request;
use app\api\model;
use think\Session;

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: token, Origin, X-Requested-With, Content-Type, Accept, Authorization");
header('Access-Control-Allow-Methods: POST,GET,PUT,DELETE');

if(request()->isOptions()){
    exit();
}

class Commodity extends Controller{
    //发布商品帖子
    public function post(Request $request){
        $res = $request->post();
        $title = $res['title'];
        $description = $res['description'];
        $price = $res['price'];
        $location = $res['location'];
        $contact = $res['contact'];

        $commodityInfo = new model\CommodityInfo;
        $result = $commodityInfo->commodityInsert($title,$description,$price,$location,$contact);

        if($result){
            return json([
                'resultCode' => 1,
                'msg' => 'success'
            ]);
        }else{
            return json([
                'resultCode' => 0,
                'msg' => 'failed'
            ]);
        }

    }


    //首页展示最新帖子
    public function browse(){





    }



}