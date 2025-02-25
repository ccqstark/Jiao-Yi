<?php
namespace app\api\controller;
use think\Controller;
use think\Request;
use app\api\model;
use think\Session;
//CORS跨域
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: token, Origin, X-Requested-With, Content-Type, Accept, Authorization");
header('Access-Control-Allow-Methods: POST,GET,PUT,DELETE');
if(request()->isOptions()){
    exit();
}
//防XSS
// ini_set("session.cookie_httponly", 1);

class Commodity extends Controller{
    //发布商品帖子
    public function post(Request $request){
        $res = $request->post();
        $author_id = Session::get('user_id'); //当前用户
        $token_v = $res['token'];
        //token验证,防CSRF
        $token = model\Token::getToken($author_id);
        if($token_v != $token){
            return json([
                'resultCode' => -100,
                'msg' => 'invalid token'
            ]);
        }

        $title = $res['title'];
        $description = $res['description'];
        $price = $res['price'];
        $location = $res['location'];
        $contact = $res['contact'];
        //插入数据库
        $commodityInfo = new model\CommodityBaseInfo;
        $result = $commodityInfo->commodityInsert($author_id, $title, $description, 
                                                        $price, $location, $contact);

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
    public function browseFirst(){

        $commodityModel = new model\CommodityBaseInfo;
        $data = $commodityModel->getFirstPage();
        
        return json($data);
    }

    //下一页
    public function browseNext(){

        $commodityModel = new model\CommodityBaseInfo;
        $data = $commodityModel->getNextPage();
        
        return json($data);
    }

    public function detail(Request $request){

        $res = $request->post();
        $commodity_id = $res['commodity_id'];

        $commodityModel = new model\CommodityBaseInfo;
        $target_commodity = $commodityModel->getDetail($commodity_id);

        if(!$target_commodity){  //查无此商品
            return json([
                'resultCode' => 0,
                'msg' => 'commodity not found'
            ]);
        }else{
            return json([
                'resultCode' => 1,
                'commodity' => $target_commodity,
                'msg' => 'success'  
            ]);
        }

    }

    //收藏、点赞
    public function likeIt(Request $request){

        $res = $request->post();
        $new_like = $res['commodity_id'];

        $likeModel = new model\CommodityBaseInfo;
        $result = $likeModel->addFavo($new_like);

        if(!$result){
            return json([
                'resultCode'=>0,
                'msg' => 'failed'
            ]);
        }else{
            return json([
                'resultCode'=>1,
                'msg' => 'success'
            ]);
        }
    }

    



}